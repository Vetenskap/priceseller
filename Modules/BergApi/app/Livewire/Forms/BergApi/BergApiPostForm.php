<?php

namespace Modules\BergApi\Livewire\Forms\BergApi;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\BergApi\Models\BergApi;

class BergApiPostForm extends Form
{
    public ?BergApi $bergApi = null;

    #[Validate]
    public $api_key;
    #[Validate]
    public $supplier_id;

    public function setBergApi(?BergApi $bergApi): void
    {
        $this->bergApi = $bergApi;
        if ($bergApi) {
            $this->api_key = $bergApi->api_key;
            $this->supplier_id = $bergApi->supplier_id;
        }
    }

    public function rules(): array
    {
        return [
            'api_key' => [
                'required',
                'string',
                Rule::unique('berg_apis', 'api_key')
                    ->when($this->bergApi, fn (Unique $unique) => $unique->ignore($this->bergApi->id, 'id'))
            ],
            'supplier_id' => ['required', 'uuid', 'exists:suppliers,id']
        ];
    }

    public function store(): void
    {
        $this->validate();

        if ($this->bergApi) {
            $this->bergApi->update($this->only([
                'api_key',
                'supplier_id'
            ]));
        } else {
            $this->bergApi = auth()->user()->bergApi()->create($this->only([
                'api_key',
                'supplier_id'
            ]));
        }
    }
}
