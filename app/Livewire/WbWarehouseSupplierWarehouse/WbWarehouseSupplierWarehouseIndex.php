<?php

namespace App\Livewire\WbWarehouseSupplierWarehouse;

use App\Models\WbWarehouseSupplier;
use App\Models\WbWarehouseSupplierWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class WbWarehouseSupplierWarehouseIndex extends Component
{
    public WbWarehouseSupplier $supplier;

    #[Validate]
    public $supplier_warehouse_id;

    public function store(): void
    {
        $this->validate();

        $this->supplier->warehouses()->create($this->except(['supplier']));
    }

    public function destroy(int $id): void
    {
        WbWarehouseSupplierWarehouse::findOrFail($id)->delete();
    }

    public function rules(): array
    {
        return [
            'supplier_warehouse_id' => [
                'required',
                'exists:supplier_warehouses,id',
                Rule::unique('wb_warehouse_supplier_warehouses', 'supplier_warehouse_id')
                    ->where('wb_warehouse_supplier_id', $this->supplier->id)
            ],
        ];
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-warehouse-supplier-warehouse.wb-warehouse-supplier-warehouse-index');
    }
}
