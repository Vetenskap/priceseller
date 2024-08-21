<?php

namespace Modules\VoshodApi\Livewire\VoshodApiWarehouse;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\VoshodApi\Models\VoshodApi;
use Modules\VoshodApi\Rules\VoshodApiWarehouse;

class VoshodApiWarehouseIndex extends Component
{
    public VoshodApi $voshodApi;

    public $name;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                new VoshodApiWarehouse,
                Rule::unique('voshod_api_warehouses', 'name')
                    ->where('voshod_api_id', $this->voshodApi->id)
            ]
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->voshodApi->warehouses()->create(['name' => $this->name]);
    }

    #[On('delete-warehouse')]
    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('voshodapi::livewire.voshod-api-warehouse.voshod-api-warehouse-index', [
            'configWarehouses' => config('voshodapi.warehouses')
        ]);
    }
}
