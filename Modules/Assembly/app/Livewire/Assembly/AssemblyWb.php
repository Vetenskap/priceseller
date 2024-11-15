<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\HttpClient\WbClient\Resources\Order;
use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithSort;
use App\Models\Item;
use App\Models\WbMarket;
use Illuminate\Support\Collection;
use Modules\Assembly\Services\AssemblyWbService;
use Opcodes\LogViewer\Facades\Cache;

class AssemblyWb extends ModuleComponent
{
    use WithSort;

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

        AssemblyWbService::createSupply($this->market, $this->supplyName, $this->selectedOrders->filter()->keys());
    }

    public function updatedSortBy(): void
    {
        if ($this->sortDirection === 'asc') {
            $this->orders = $this->orders->sortBy(function (Order $order) {
                try {
                    return $order->{'get' . \Illuminate\Support\Str::apa($this->sortBy)}($this->currentUser());
                } catch (\Error $e) {
                    try {
                        return $order->getCard()->{'get' . \Illuminate\Support\Str::apa($this->sortBy)}();
                    } catch (\Error $e) {
                        if (isset($order->getCard()->getProduct()[$this->sortBy])) {
                            return $order->getCard()->getProduct()[$this->sortBy];
                        } else {
                            if ($order->getCard()->getProduct()->itemable instanceof Item) {
                                return $order->getCard()->getProduct()->itemable[$this->sortBy];
                            } else {
                                return $order->getCard()->getProduct()->itemable->items->sortBy(fn (Item $item) => $item[$this->sortBy])->first()[$this->sortBy];
                            }
                        }
                    }
                }
            });
        } else {
            $this->orders = $this->orders->sortByDesc(function (Order $order) {
                try {
                    return $order->{'get' . \Illuminate\Support\Str::apa($this->sortBy)}($this->currentUser());
                } catch (\Error $e) {
                    try {
                        return $order->getCard()->{'get' . \Illuminate\Support\Str::apa($this->sortBy)}();
                    } catch (\Error $e) {
                        if (isset($order->getCard()->getProduct()[$this->sortBy])) {
                            return $order->getCard()->getProduct()[$this->sortBy];
                        } else {
                            if ($order->getCard()->getProduct()->itemable instanceof Item) {
                                return $order->getCard()->getProduct()->itemable[$this->sortBy];
                            } else {
                                return $order->getCard()->getProduct()->itemable->items->sortByDesc(fn (Item $item) => $item[$this->sortBy])->first()[$this->sortBy];
                            }
                        }
                    }
                }
            });
        }
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
    }

    public function loadOrders(): void
    {
        $this->orders = Cache::rememberForever('test', function () {
            $list = new Order();
            $orders = $list->getNewAll($this->market);
            return $orders->map(function (Order $order) {
                $order->fetchCard($this->market->api_key);
                $order->getCard()->loadLink($this->market);
                return $order;
            });
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
