<?php

namespace MoyskladSupplier;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladSupplierSupplier;

class MoyskladSupplierPostForm extends Form
{
    public Moysklad $moysklad;
    public ?MoyskladSupplierSupplier $moyskladSupplier = null;

    #[Validate]
    public $moysklad_supplier_uuid;
    #[Validate]
    public $supplier_id;

    public function rules(): array
    {
        return [
            'moysklad_supplier_uuid' => [
                'required',
                'uuid',
                Rule::unique('moysklad_supplier_suppliers', 'moysklad_supplier_uuid')
                    ->ignore($this->moyskladSupplier),
            ],
            'supplier_id' => [
                'required',
                'uuid',
                'exists:suppliers,id',
                Rule::unique('moysklad_supplier_suppliers', 'supplier_id')
                    ->ignore($this->moyskladSupplier),
            ],
        ];
    }

    public function setMoyskladSupplier(MoyskladSupplierSupplier $moyskladSupplier): void
    {
        $this->moyskladSupplier = $moyskladSupplier;
        $this->moysklad_supplier_uuid = $moyskladSupplier->moysklad_supplier_uuid;
        $this->supplier_id = $moyskladSupplier->supplier_id;
    }

    public function setMoysklad(Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function store(): void
    {
        $this->validate();

        $this->moysklad->suppliers()->create($this->except(['moysklad', 'moyskladSupplier']));

        $this->reset(['moysklad_supplier_uuid', 'supplier_id']);
    }

    public function update(): void
    {
        $this->validate();

        $this->moyskladSupplier->update($this->except(['moysklad', 'moyskladSupplier']));
    }

    public function destroy(): void
    {
        $this->moyskladSupplier->delete();
    }
}
