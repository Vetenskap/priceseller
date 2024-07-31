<?php

namespace Modules\Moysklad\Livewire\MoyskladWarehouse;

use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladWarehouseIndex extends Component
{
    public Moysklad $moysklad;

    public Collection $moyskladWarehouses;

    public $warehouseId;

    public function save()
    {
        $this->dispatch('save.moysklad.warehouses');
    }

    public function mount()
    {
        $service = new MoyskladService($this->moysklad);
        $this->moyskladWarehouses = $service->getAllWarehouses();
    }

    public function add()
    {
        $this->moysklad->warehouses()->updateOrCreate([
            'warehouse_id' => $this->warehouseId
        ], [
            'warehouse_id' => $this->warehouseId
        ]);
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-warehouse.moysklad-warehouse-index');
    }
}
