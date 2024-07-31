<?php

namespace App\Livewire\WbWarehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Models\WbMarket;
use App\Models\WbWarehouse;

class WbWarehouseIndex extends BaseComponent
{
    public WbMarket $market;
    public $apiWarehouses;

    public $selectedWarehouse;

    public function addWarehouse()
    {
        if (!$this->selectedWarehouse) {
            $this->js((new Toast('Ошибка', "Не выбран склад"))->danger());
            return;
        }

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
