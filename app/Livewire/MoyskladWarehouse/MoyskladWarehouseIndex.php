<?php

namespace App\Livewire\MoyskladWarehouse;

use App\Models\Moysklad;
use App\Models\MoyskladWarehouse;
use Livewire\Component;

class MoyskladWarehouseIndex extends Component
{
    public Moysklad $moysklad;
    public $apiWarehouses;

    public $selectedWarehouse;

    public function mount()
    {
        $this->selectedWarehouse = $this->apiWarehouses->first()?->get('id');
    }

    public function addWarehouse()
    {
        $name = $this->apiWarehouses->firstWhere('id', $this->selectedWarehouse)->get('name');

        $this->moysklad->warehouses()->updateOrCreate([
            'ms_uuid' => $this->selectedWarehouse
        ], [
            'ms_uuid' => $this->selectedWarehouse,
            'name' => $name
        ]);
    }

    public function destroy(array $warehouse)
    {
        $warehouse = MoyskladWarehouse::findOrFail($warehouse['id']);

        $warehouse->delete();
    }

    public function render()
    {
        return view('livewire.moysklad-warehouse.moysklad-warehouse-index');
    }
}
