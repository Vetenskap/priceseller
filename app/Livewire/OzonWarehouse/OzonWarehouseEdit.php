<?php

namespace App\Livewire\OzonWarehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseSupplier;
use App\Models\OzonWarehouseUserWarehouse;

class OzonWarehouseEdit extends BaseComponent
{
    use WithJsNotifications;

    public OzonWarehouse $warehouse;

    public $name;

    public $warehouse_id;

    public $selectedTab;

    public $selectedSupplier;
    public $selectedWarehouse;

    public function save()
    {
        $this->authorize('update', $this->warehouse);

        $this->warehouse->update($this->only('name'));

        $this->addSuccessSaveNotification();
    }

    public function mount()
    {
        $this->name = $this->warehouse->name;
        $this->warehouse_id = $this->warehouse->warehouse_id;
    }

    public function addSupplier()
    {
        if (!$this->selectedSupplier) {
            $this->js((new Toast('Ошибка', "Не выбран поставщик"))->danger());
            return;
        }

        $this->warehouse->suppliers()->updateOrCreate([
            'supplier_id' => $this->selectedSupplier
        ], [
            'supplier_id' => $this->selectedSupplier
        ]);
    }

    public function addWarehouse()
    {
        if (!$this->selectedWarehouse) {
            $this->js((new Toast('Ошибка', "Не выбран склад пользователя"))->danger());
            return;
        }

        $this->warehouse->userWarehouses()->updateOrCreate([
            'warehouse_id' => $this->selectedWarehouse
        ], [
            'warehouse_id' => $this->selectedWarehouse
        ]);
    }

    public function deleteSupplier(array $supplier)
    {
        $supplier = OzonWarehouseSupplier::findOrFail($supplier['id']);
        $supplier->delete();
    }

    public function deleteWarehouse(array $warehouse)
    {
        $warehouse = OzonWarehouseUserWarehouse::findOrFail($warehouse['id']);
        $warehouse->delete();
    }

    public function destroy()
    {
        $this->authorize('delete', $this->warehouse);

        $this->warehouse->delete();
    }

    public function render()
    {
        $this->authorize('view', $this->warehouse);

        return view('livewire.ozon-warehouse.ozon-warehouse-edit');
    }
}
