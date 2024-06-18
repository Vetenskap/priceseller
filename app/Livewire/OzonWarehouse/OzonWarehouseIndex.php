<?php

namespace App\Livewire\OzonWarehouse;

use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use Livewire\Component;

class OzonWarehouseIndex extends Component
{
    public OzonMarket $market;
    public $apiWarehouses;

    public $selectedWarehouse;

    public function mount()
    {
        $firstWarehouse = collect($this->apiWarehouses)->first();
        $this->selectedWarehouse = $firstWarehouse ? $firstWarehouse['warehouse_id'] : null;
    }

    public function addWarehouse()
    {
        $this->authorize('create', OzonWarehouse::class);

        $name = collect($this->apiWarehouses)->firstWhere('warehouse_id', $this->selectedWarehouse)['name'];

        $this->market->warehouses()->updateOrCreate([
            'warehouse_id' => $this->selectedWarehouse,
        ], [
            'warehouse_id' => $this->selectedWarehouse,
            'name' => $name
        ]);
    }

    public function destroy(array $warehouse)
    {
        $warehouse = OzonWarehouse::findOrFail($warehouse['id']);

        $this->authorize('delete', $warehouse);

        $warehouse->delete();
    }

    public function render()
    {
        return view('livewire.ozon-warehouse.ozon-warehouse-index');
    }
}
