<?php

namespace App\Livewire\Forms\Supplier;

use App\Helpers\Helpers;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SupplierPostForm extends Form
{
    public ?Supplier $supplier = null;

    #[Validate]
    public $name;

    public $open = false;

    public $use_brand = false;

    public $unload_without_price = false;

    public function setSupplier(Supplier $supplier): void
    {
        $this->supplier = $supplier;
        $this->name = $supplier->name;
        $this->open = (bool) $supplier->open;
        $this->use_brand = (bool) $supplier->use_brand;
        $this->unload_without_price = (bool) $supplier->unload_without_price;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'min:3',
                'string',
                Rule::unique('suppliers', 'name')
                    ->where('user_id', Helpers::user()->id)
                ->when($this->supplier, fn (Unique $unique) => $unique->ignore($this->supplier->id, 'id'))
            ],
            'open' => ['nullable', 'boolean'],
            'use_brand' => ['nullable', 'boolean'],
            'unload_without_price' => ['nullable', 'boolean'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        Helpers::user()->suppliers()->create($this->except('supplier'));

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->supplier->update($this->except('supplier'));
    }

    public function destroy(): void
    {
        DB::transaction(function () {
            $this->supplier->delete();
        });
    }
}
