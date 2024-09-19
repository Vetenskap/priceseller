<?php

namespace App\Livewire\Forms\SupplierWarehouse;

use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SupplierWarehousePostForm extends Form
{
    public ?SupplierWarehouse $supplierWarehouse = null;
    public Supplier $supplier;

    #[Validate]
    public $name;

    public function setSupplierWarehouse(SupplierWarehouse $supplierWarehouse): void
    {
        $this->supplierWarehouse = $supplierWarehouse;
        $this->name = $supplierWarehouse->name;
    }

    public function setSupplier(Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('supplier_warehouses', 'name')
                    ->where('supplier_id', $this->supplier->id),
            ]
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->supplier->warehouses()->create($this->except(['supplier', 'supplierWarehouse']));
    }

    public function update(): void
    {
        $this->validate();

        $this->supplierWarehouse->update($this->except(['supplier', 'supplierWarehouse']));
    }

    public function destroy(): void
    {
        $this->supplierWarehouse->delete();
    }
}
