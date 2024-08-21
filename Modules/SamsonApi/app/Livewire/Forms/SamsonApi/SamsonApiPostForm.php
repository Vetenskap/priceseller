<?php

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\SamsonApi\Models\SamsonApi;

class SamsonApiPostForm extends Form
{
    public ?SamsonApi $samsonApi = null;

    #[Validate]
    public $api_key = null;
    #[Validate]
    public $supplier_id = null;

    public function rules(): array
    {
        return [
            'api_key' => [
                'required',
                'string',
                Rule::unique('voshod_apis', 'api_key')
                    ->when($this->samsonApi, fn (Unique $unique) => $unique->ignore($this->samsonApi->id, 'id'))
            ],
            'supplier_id' => [
                'required',
                'uuid',
                'exists:suppliers,id'
            ]
        ];
    }

    public function setSamsonApi(?SamsonApi $samsonApi): void
    {
        $this->samsonApi = $samsonApi;
        if ($samsonApi) {
            $this->api_key = $samsonApi->api_key;
            $this->supplier_id = $samsonApi->supplier_id;
        }
    }

    public function store(): void
    {
        $this->validate();

        if ($this->samsonApi) {
            $this->samsonApi->update($this->except('samsonApi'));
        } else {
            $this->samsonApi = auth()->user()->samsonApi()->create($this->except('samsonApi'));
        }
    }
}
