<?php

namespace App\Livewire\OzonWarehouse;

use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseSupplier;
use Livewire\Component;

class OzonWarehouseEdit extends Component
{
    public OzonWarehouse $warehouse;

    public $name;

    public $warehouse_id;

    public $selectedTab;

    public $selectedSupplier;

    public function mount()
    {
        $this->name = $this->warehouse->name;
        $this->warehouse_id = $this->warehouse->warehouse_id;
        $this->selectedSupplier = auth()->user()->suppliers->first()?->id;
    }

    public function addSupplier()
    {
        $this->warehouse->suppliers()->updateOrCreate([
            'supplier_id' => $this->selectedSupplier
        ], [
            'supplier_id' => $this->selectedSupplier
        ]);
    }

    public function deleteSupplier(array $supplier)
    {
        $supplier = OzonWarehouseSupplier::findOrFail($supplier['id']);
        $supplier->delete();
    }

    public function destroy()
    {
        $this->authorize('delete', $this->warehouse);

        $this->warehouse->delete();
    }

    public function render()
    {
        return view('livewire.ozon-warehouse.ozon-warehouse-edit');
    }
}
