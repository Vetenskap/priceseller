<?php

namespace Modules\VoshodApi\Livewire\VoshodApiWarehouse;

use App\Livewire\BaseComponent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use LaravelIdea\Helper\Modules\VoshodApi\Models\_IH_VoshodApiWarehouse_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\VoshodApi\Models\VoshodApi;
use Modules\VoshodApi\Rules\VoshodApiWarehouse;

class VoshodApiWarehouseIndex extends BaseComponent
{
    use WithPagination;

    public VoshodApi $voshodApi;

    public $name;

    public $supplier_warehouse_id;

    #[Computed]
    public function warehouses(): array|LengthAwarePaginator|_IH_VoshodApiWarehouse_C|\Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->voshodApi
            ->warehouses()
            ->paginate();
    }

    public function destroy($id): void
    {
        $warehouse = $this->voshodApi->warehouses()->find($id);
        $warehouse->delete();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                new VoshodApiWarehouse,
                Rule::unique('voshod_api_warehouses', 'name')
                    ->where('voshod_api_id', $this->voshodApi->id)
            ],
            'supplier_warehouse_id' => [
                'required',
                'uuid',
                Rule::unique('voshod_api_warehouses', 'supplier_warehouse_id')
                    ->where('voshod_api_id', $this->voshodApi->id)
            ]
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->voshodApi->warehouses()->create([
            'name' => $this->name,
            'supplier_warehouse_id' => $this->supplier_warehouse_id
        ]);
    }

    #[On('delete-warehouse')]
    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('voshodapi::livewire.voshod-api-warehouse.voshod-api-warehouse-index', [
            'configWarehouses' => config('voshodapi.warehouses')
        ]);
    }
}
