<?php

namespace App\Livewire\Forms\EmailSupplierWarehouse;

use App\Models\EmailSupplier;
use App\Models\EmailSupplierWarehouse;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailSupplierWarehousePostForm extends Form
{
    public ?EmailSupplier $emailSupplier = null;

    public EmailSupplierWarehouse $emailSupplierWarehouse;

    #[Validate]
    public $value;

    #[Validate]
    public $supplier_warehouse_id;

    public function setEmailSupplier(EmailSupplier $emailSupplier): void
    {
        $this->emailSupplier = $emailSupplier;
    }

    public function setEmailSupplierWarehouse(EmailSupplierWarehouse $emailSupplierWarehouse): void
    {
        $this->emailSupplierWarehouse = $emailSupplierWarehouse;
        $this->value = $emailSupplierWarehouse->value;
        $this->supplier_warehouse_id = $emailSupplierWarehouse->supplier_warehouse_id;
    }

    public function rules(): array
    {
        return [
            'value' => ['required', 'string'],
            'supplier_warehouse_id' => ['required', 'uuid', 'exists:supplier_warehouses,id'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->emailSupplier->warehouses()->create($this->except(['emailSupplier', 'emailSupplierWarehouse']));
    }

    public function update(): void
    {
        $this->validate();

        $this->emailSupplierWarehouse->update($this->except(['emailSupplier', 'emailSupplierWarehouse']));
    }

    public function destroy(): void
    {
        $this->emailSupplierWarehouse->delete();
    }
}
