<?php

namespace App\Livewire\WbWarehouse;

use App\Models\WbMarket;
use App\Models\WbWarehouse;
use Livewire\Component;

class WbWarehouseIndex extends Component
{
    public WbMarket $market;
    public $apiWarehouses;

    public $selectedWarehouse;

    public function mount()
    {
        $this->selectedWarehouse = collect($this->apiWarehouses)->first()['id'];
    }

    public function addWarehouse()
    {
        $this->authorize('create', WbWarehouse::class);

        $name = collect($this->apiWarehouses)->firstWhere('id', $this->selectedWarehouse)['name'];

        $this->market->warehouses()->updateOrCreate([
            'warehouse_id' => $this->selectedWarehouse,
        ], [
            'warehouse_id' => $this->selectedWarehouse,
            'name' => $name
        ]);
    }

    public function destroy(array $warehouse)
    {
        $warehouse = WbWarehouse::findOrFail($warehouse['id']);

        $this->authorize('delete', $warehouse);

        $warehouse->delete();
    }

    public function render()
    {
        return view('livewire.wb-warehouse.wb-warehouse-index');
    }
}
