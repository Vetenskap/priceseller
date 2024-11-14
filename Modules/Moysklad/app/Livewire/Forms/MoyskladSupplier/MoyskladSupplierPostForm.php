<?php

namespace Modules\Moysklad\Livewire\Forms\MoyskladSupplier;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladSupplierSupplier;

class MoyskladSupplierPostForm extends Form
{
    public Moysklad $moysklad;

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
                'unique:moysklad_supplier_suppliers,moysklad_supplier_uuid',
            ],
            'supplier_id' => [
                'required',
                'uuid',
                'exists:suppliers,id',
                'unique:moysklad_supplier_suppliers,supplier_id',
            ],
        ];
    }

    public function setMoysklad(Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function store(): void
    {
        $this->validate();

        $this->moysklad->suppliers()->create($this->except(['moysklad']));

        $this->reset(['moysklad_supplier_uuid', 'supplier_id']);
    }
}
