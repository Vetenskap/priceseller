<?php

namespace App\Livewire\OzonWarehouseSupplierWarehouse;

use App\Models\OzonWarehouseSupplier;
use App\Models\OzonWarehouseSupplierWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class OzonWarehouseSupplierWarehouseIndex extends Component
{
    public OzonWarehouseSupplier $supplier;

    #[Validate]
    public $supplier_warehouse_id;

    public function store(): void
    {
        $this->validate();

        $this->supplier->warehouses()->create($this->except(['supplier']));
    }

    public function destroy(int $id): void
    {
        OzonWarehouseSupplierWarehouse::findOrFail($id)->delete();
    }

    public function rules(): array
    {
        return [
            'supplier_warehouse_id' => [
                'required',
                'exists:supplier_warehouses,id',
                Rule::unique('ozon_warehouse_supplier_warehouses', 'supplier_warehouse_id')
                    ->where('ozon_warehouse_supplier_id', $this->supplier->id)
            ],
        ];
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-warehouse-supplier-warehouse.ozon-warehouse-supplier-warehouse-index');
    }
}
