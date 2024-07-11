<?php

namespace App\Livewire\OzonWarehouse;

use App\Livewire\Components\Toast;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use Livewire\Component;

class OzonWarehouseIndex extends Component
{
    public OzonMarket $market;
    public $apiWarehouses;

    public $selectedWarehouse = null;

    public function addWarehouse()
    {
        if (!$this->selectedWarehouse) {
            $this->js((new Toast('Ошибка', "Не выбран склад"))->danger());
            return;
        }

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
