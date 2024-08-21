<?php

namespace App\Livewire\Forms\EmailSupplier;

use App\Models\Email;
use App\Models\EmailSupplier;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailSupplierPostForm extends Form
{
    public Email $mainEmal;
    public ?EmailSupplier $emailSupplier = null;

    #[Validate]
    public $supplier_id;

    #[Validate]
    public $email;

    #[Validate]
    public $filename;

    #[Validate]
    public $header_article;

    #[Validate]
    public $header_brand;

    #[Validate]
    public $header_price;

    #[Validate]
    public $header_count;

    public function setMainEmal(Email $mainEmal): void
    {
        $this->mainEmal = $mainEmal;
    }

    public function setEmailSupplier(EmailSupplier $emailSupplier): void
    {
        $this->emailSupplier = $emailSupplier;
        $this->supplier_id = $emailSupplier->supplier_id;
        $this->email = $emailSupplier->email;
        $this->filename = $emailSupplier->filename;
        $this->header_price = $emailSupplier->header_price;
        $this->header_article = $emailSupplier->header_article;
        $this->header_brand = $emailSupplier->header_brand;
        $this->header_count = $emailSupplier->header_count;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                Rule::unique('email_suppliers', 'supplier_id')
                    ->when($this->emailSupplier, fn(Unique $unique) => $unique->ignore($this->emailSupplier->id, 'id'))
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('email_suppliers', 'email')
                    ->where('supplier_id', $this->supplier_id)
                    ->when($this->emailSupplier, fn(Unique $unique) => $unique->ignore($this->emailSupplier->id, 'id')),
            ],
            'filename' => ['required', 'string'],
            'header_article' => ['required', 'integer'],
            'header_brand' => ['nullable', 'integer'],
            'header_price' => ['required', 'integer'],
            'header_count' => ['required', 'integer'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        $date = [
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $this->mainEmal->suppliers()->attach($this->supplier_id, array_merge($date, $this->except(['mainEmal', 'emailSupplier'])));

        $this->reset('supplier_id', 'email', 'filename', 'header_article', 'header_brand', 'header_price', 'header_count');
    }

    public function update(): void
    {
        $this->validate();

        $this->emailSupplier->update($this->except(['mainEmal', 'emailSupplier']));

    }

    public function destroy(string $id): void
    {
        $this->mainEmal->suppliers()->detach($id);
    }
}
