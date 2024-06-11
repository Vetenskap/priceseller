<?php

namespace App\Livewire\WbWarehouse;

use App\Models\Supplier;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseSupplier;
use Livewire\Component;

class WbWarehouseEdit extends Component
{
    public WbWarehouse $warehouse;

    public $name;

    public $warehouse_id;

    public $selectedTab;

    public $selectedSupplier;

    public function mount()
    {
        $this->name = $this->warehouse->name;
        $this->warehouse_id = $this->warehouse->warehouse_id;
        $this->selectedSupplier = auth()->user()->suppliers->first()->id;
    }

    public function render()
    {
        return view('livewire.wb-warehouse.wb-warehouse-edit');
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
        $supplier = WbWarehouseSupplier::findOrFail($supplier['id']);
        $supplier->delete();
    }
}
