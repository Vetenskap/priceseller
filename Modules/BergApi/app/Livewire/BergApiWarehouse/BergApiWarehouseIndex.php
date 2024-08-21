<?php

namespace Modules\BergApi\Livewire\BergApiWarehouse;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\BergApi\Models\BergApi;

class BergApiWarehouseIndex extends Component
{
    public BergApi $bergApi;

    public $name;
    public $warehouse_id;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('berg_api_warehouses', 'name')
                    ->where('berg_api_id', $this->bergApi->id)
            ],
            'warehouse_id' => [
                'required',
                'integer',
                Rule::unique('berg_api_warehouses', 'warehouse_id')
                    ->where('berg_api_id', $this->bergApi->id)
            ]
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->bergApi->warehouses()->create([
            'name' => $this->name,
            'warehouse_id' => $this->warehouse_id
        ]);
    }

    #[On('delete-warehouse')]
    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-warehouse.berg-api-warehouse-index');
    }
}
