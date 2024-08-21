<?php

namespace App\Livewire\Forms\Warehouse;

use App\Models\Warehouse;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class WarehousePostForm extends Form
{
    public ?Warehouse $warehouse = null;

    #[Validate]
    public $name;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                Rule::unique('warehouses', 'name')
                    ->where('user_id', auth()->user()->id)
                    ->when($this->warehouse, fn (Unique $unique) => $unique->ignore($this->warehouse->id, 'id')),
            ],
        ];
    }

    public function setWarehouse(Warehouse $warehouse): void
    {
        $this->warehouse = $warehouse;
        $this->name = $this->warehouse->name;
    }

    public function store(): void
    {
        $this->validate();

        auth()->user()->warehouses()->create($this->except('warehouse'));

        $this->reset();

    }

    public function update(): void
    {
        $this->validate();

        $this->warehouse->update($this->except('warehouse'));
    }
}
