<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\HttpClient\WbClient\Resources\Order;
use App\HttpClient\WbClient\Resources\Sticker;
use App\HttpClient\WbClient\Resources\Supply;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\Item;
use Illuminate\Support\Collection;
use Modules\Assembly\Services\AssemblyWbService;
use Opcodes\LogViewer\Facades\Cache;

class AssemblyWbSupply extends BaseComponent
{
    use WithSort;

    public $fields = [];

    public $mainFields = [];

    public $additionalFields = [];

    public ?Collection $orders = null;
    public Supply $wbSupply;
    public \Modules\Assembly\Models\AssemblyWbSupply $supply;

    public function closeSupply(): void
    {
        if (AssemblyWbService::closeSupply($this->wbSupply, $this->supply)) {
            \Flux::toast('Поставка успешно закрыта');
            $this->supply = $this->supply->refresh();
        } else {
            \Flux::toast('Не удалось закрыть поставку', variant: 'danger');
        }
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
                                return $order->getCard()->getProduct()->itemable->items->sortBy(fn(Item $item) => $item[$this->sortBy])->first()[$this->sortBy];
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
                                return $order->getCard()->getProduct()->itemable->items->sortByDesc(fn(Item $item) => $item[$this->sortBy])->first()[$this->sortBy];
                            }
                        }
                    }
                }
            });
        }
    }

    public function updatedSortDirection(): void
    {
        if ($this->sortDirection === 'asc') {
            $this->orders = $this->orders->sortBy(fn(Collection $collection) => $collection->get($this->sortBy) ?
                $collection->sortBy($this->sortBy) :
                $collection->get('card')->sortBy(fn(Collection $collection) => $collection->get($this->sortBy) ?
                    $collection->sortBy($this->sortBy) :
                    $collection->get('product')->sortBy($this->sortBy)
                )
            );
        } else {
            $this->orders = $this->orders->sortByDesc(fn(Collection $collection) => $collection->get($this->sortBy) ?
                $collection->sortBy($this->sortBy) :
                $collection->get('card')->sortByDesc(fn(Collection $collection) => $collection->get($this->sortBy) ?
                    $collection->sortBy($this->sortBy) :
                    $collection->get('product')->sortByDesc($this->sortBy)
                )
            );
        }
    }


    public function mount()
    {
        $wbSupply = new Supply();
        $wbSupply->setId($this->supply->id_supply);
        $wbSupply->fetch($this->supply->market->api_key);
        $wbSupply->fetchOrders($this->supply->market->api_key);

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

        $this->orders = Cache::rememberForever('test1', function () use ($wbSupply) {
            $ordersIds = $wbSupply->getOrders()->map(fn (Order $order) => $order->getId())->toArray();
            $stickers = Sticker::getFromOrderIds($ordersIds, $this->supply->market->api_key);

            return $wbSupply->getOrders()->map(function (Order $order) use ($stickers) {
                $order->fetchCard($this->supply->market->api_key);
                $order->getCard()->loadLink($this->supply->market);
                $order->setSticker($stickers->firstWhere(fn (Sticker $sticker) => $sticker->getOrderId() === $order->getId()));
                return $order;
            });
        });

    }

    public function render()
    {
        if (!$this->user()->can('view-assembly')) {
            abort(403);
        }

        return view('assembly::livewire.assembly.assembly-wb-supply');
    }
}
