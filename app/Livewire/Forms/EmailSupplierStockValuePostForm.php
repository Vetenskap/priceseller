<?php

namespace App\Livewire\Forms;

use App\Models\EmailSupplier;
use App\Models\EmailSupplierStockValue;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailSupplierStockValuePostForm extends Form
{
    public EmailSupplier $emailSupplier;
    public ?EmailSupplierStockValue $stockValue = null;

    #[Validate]
    public $name;

    #[Validate]
    public $value;

    //
    public function setStockValue(\App\Models\EmailSupplierStockValue $stockValue): void
    {
        $this->name = $stockValue->name;
        $this->value = $stockValue->value;
        $this->stockValue = $stockValue;
    }

    public function setEmailSupplier(EmailSupplier $emailSupplier): void
    {
        $this->emailSupplier = $emailSupplier;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('email_supplier_stock_values', 'name')
                    ->where('email_supplier_id', $this->emailSupplier->id)
                    ->when($this->stockValue, fn(Unique $unique) => $unique->ignore($this->stockValue->id, 'id'))
            ],
            'value' => ['required', 'integer']
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->emailSupplier->stockValues()->create($this->except(['emailSupplier', 'stockValue']));
    }

    public function update(): void
    {
        $this->validate();

        $this->stockValue->update($this->except(['emailSupplier', 'stockValue']));
    }

    public function destroy(): void
    {
        $this->stockValue->delete();
    }
}
