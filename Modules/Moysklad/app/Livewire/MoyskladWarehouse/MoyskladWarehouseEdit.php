<?php

namespace Modules\Moysklad\Livewire\MoyskladWarehouse;

use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladWarehouseEdit extends Component
{
    public MoyskladWarehouseWarehouse $warehouse;

    public Collection $moyskladWarehouses;

    public $moyskladWarehouseId;

    public function mount()
    {
        $this->moyskladWarehouseId = $this->warehouse->moysklad_warehouse_uuid;
    }

    #[On('save.moysklad.warehouses')]
    public function save()
    {
        $this->warehouse->moysklad_warehouse_uuid = $this->moyskladWarehouseId;
        $this->warehouse->save();
    }

    public function updateStocks()
    {
        $service = new MoyskladService($this->warehouse->moysklad);
        dd($service->getAllStocks($this->warehouse->moysklad_warehouse_uuid));
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-warehouse.moysklad-warehouse-edit');
    }
}
