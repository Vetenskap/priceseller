<?php

namespace App\Livewire\Forms\Supplier;

use App\Models\Supplier;
use Illuminate\Support\Arr;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SupplierPostForm extends Form
{
    public ?Supplier $supplier;

    #[Validate]
    public $name;

    #[Validate]
    public $ms_uuid = null;

    public $open = true;

    public $use_brand = false;

    public function setSupplier(Supplier $supplier)
    {
        $this->supplier = $supplier;
        $this->name = $supplier->name;
        $this->ms_uuid = $supplier->ms_uuid;
        $this->open = $supplier->open;
        $this->use_brand = $supplier->use_brand;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'min:3', 'string'],
            'ms_uuid' => ['nullable', 'uuid'],
        ];
    }

    public function store()
    {
        $supplier = Supplier::create(Arr::add($this->except('supplier'), 'user_id', auth()->user()->id));

        $supplier->refresh();

        return $supplier;
    }

    public function update()
    {
        $this->supplier->update($this->except('supplier'));
    }
}
