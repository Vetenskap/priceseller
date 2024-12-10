<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\HttpClient\WbClient\Resources\Order;
use App\Livewire\ModuleComponent;
use App\Models\Item;
use App\Models\WbMarket;
use Illuminate\Support\Collection;
use Modules\Assembly\Services\AssemblyWbService;

class AssemblyWb extends ModuleComponent
{

    public $fields = [];

    public $mainFields = [];

    public $additionalFields = [];

    public ?Collection $orders = null;

    public WbMarket $market;

    public $selectedOrders = [];

    public $supplyName = "";

    public function createSupply(): void
    {
        $this->validate([
            'supplyName' => 'required|string|min:3|max:255',
            'selectedOrders' => 'required|array|min:1'
        ], [
            'supplyName.required' => 'Необходимо указать имя поставки',
            'supplyName.min' => 'Имя поставки должно содержать не менее 3 символов',
            'supplyName.max' => 'Имя поставки должно содержать не более 255 символов',
            'selectedOrders.required' => 'Необходимо выбрать хотя бы один заказ',
            'selectedOrders.min' => 'Необходимо выбрать хотя бы один заказ',
        ]);

        AssemblyWbService::createSupply($this->market, $this->supplyName, collect($this->selectedOrders)->filter()->keys());

        \Flux::modal('create-supply')->close();
        \Flux::toast('Поставка успешно создана');
    }

    public function sort(): void
    {
        $this->orders = $this->orders->sort(function (Order $a, Order $b) {
            // 1. Сортировка по наличию остатка (stock > 0 первыми)
            $stockA = $this->getStock($a);
            $stockB = $this->getStock($b);

            if (($stockA > 0) && ($stockB <= 0)) {
                return -1; // $a идет выше $b
            }

            if (($stockA <= 0) && ($stockB > 0)) {
                return 1; // $b идет выше $a
            }

            // 2. Сортировка по дате создания (от старых к новым)
            $dateA = $a->getCreatedAt($this->currentUser());
            $dateB = $b->getCreatedAt($this->currentUser());

            return strtotime($dateA) <=> strtotime($dateB);
        });
    }

    private function getStock(Order $order): int
    {
        if ($order->getCard()->getProduct()?->itemable instanceof Item) {
            return $order->getCard()->getProduct()?->itemable->warehousesStocks()->sum('stock') ?? 0;
        }

        return $order->getCard()->getProduct()?->itemable->items
            ->sortBy(fn(Item $item) => $item->warehousesStocks()->sum('stock'))
            ->first()?->warehousesStocks()->sum('stock') ?? 0;
    }

    public function mount(): void
    {
        $this->fields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'wb')
            ->whereNot('type', 'main')
            ->where('additional', false)
            ->orderBy('index')
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->additionalFields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'wb')
            ->where('additional', true)
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->mainFields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'wb')
            ->where('type', 'main')
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->loadOrders();

        $this->sort();
    }

    public function loadOrders(): void
    {
        $list = new Order();
        $orders = $list->getNewAll($this->market);
        $this->orders = $orders->map(function (Order $order) {
            $order->fetchCard($this->market->api_key);
            $order->getCard()->loadLink($this->market);
            return $order;
        });
    }

    public function render()
    {
        if (!$this->user()->can('view-assembly')) {
            abort(403);
        }

        return view('assembly::livewire.assembly.assembly-wb');
    }
}
