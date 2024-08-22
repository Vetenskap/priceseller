<?php

namespace Modules\VoshodApi\Livewire\Forms\VoshodApi;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\VoshodApi\Models\VoshodApi;

class VoshodApiPostForm extends Form
{
    public ?VoshodApi $voshodApi = null;

    #[Validate]
    public $api_key = null;
    #[Validate]
    public $proxy_ip = null;
    #[Validate]
    public $proxy_port = null;
    #[Validate]
    public $proxy_login = null;
    #[Validate]
    public $proxy_password = null;
    #[Validate]
    public $supplier_id = null;

    public function rules(): array
    {
        return [
            'api_key' => [
                'required',
                'string',
                Rule::unique('voshod_apis', 'api_key')
                    ->when($this->voshodApi, fn (Unique $unique) => $unique->ignore($this->voshodApi->id, 'id'))
            ],
            'proxy_ip' => ['required', 'string'],
            'proxy_port' => ['required', 'integer'],
            'proxy_login' => ['required', 'string'],
            'proxy_password' => ['required', 'string'],
            'supplier_id' => ['required', 'uuid', 'exists:suppliers,id']
        ];
    }

    public function setVoshodApi(?VoshodApi $voshodApi): void
    {
        $this->voshodApi = $voshodApi;
        if ($voshodApi) {
            $this->api_key = $voshodApi->api_key;
            $this->proxy_ip = $voshodApi->proxy_ip;
            $this->proxy_port = $voshodApi->proxy_port;
            $this->proxy_login = $voshodApi->proxy_login;
            $this->proxy_password = $voshodApi->proxy_password;
            $this->supplier_id = $voshodApi->supplier_id;
        }
    }

    public function store(): void
    {
        $this->validate();

        if ($this->voshodApi) {
            $this->voshodApi->update($this->only([
                'api_key',
                'proxy_ip',
                'proxy_port',
                'proxy_login',
                'proxy_password',
                'supplier_id',
            ]));
        } else {
            $this->voshodApi = auth()->user()->voshodApi()->create($this->only([
                'api_key',
                'proxy_ip',
                'proxy_port',
                'proxy_login',
                'proxy_password',
                'supplier_id',
            ]));
        }
    }
}
