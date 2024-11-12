<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\HttpClient\WbClient\Resources\Order;
use App\Livewire\ModuleComponent;
use App\Livewire\Traits\WithSort;
use App\Models\WbWarehouse;
use Illuminate\Support\Collection;
use Opcodes\LogViewer\Facades\Cache;

class AssemblyWb extends ModuleComponent
{
    use WithSort;

    public $fields = [];

    public $mainFields = [];

    public $additionalFields = [];

    public ?Collection $orders = null;

    public WbWarehouse $warehouse;

    public function updatedSortBy(): void
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
            $orders = $list->getNewAll($this->warehouse->market->api_key);
            return $orders->map(fn(Order $order) => $order->toCollection($this->warehouse->market));
        });
    }

    public function render()
    {
        return view('assembly::livewire.assembly.assembly-wb');
    }
}
