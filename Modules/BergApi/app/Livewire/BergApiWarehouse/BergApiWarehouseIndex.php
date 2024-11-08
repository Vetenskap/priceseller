<?php

namespace Modules\BergApi\Livewire\BergApiWarehouse;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\BergApi\Models\BergApi;

class BergApiWarehouseIndex extends Component
{
    use WithPagination;

    public BergApi $bergApi;

    public $name;
    public $warehouse_name;
    public $supplier_warehouse_id;

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('berg_api_warehouses', 'name')
                    ->where('berg_api_id', $this->bergApi->id)
            ],
            'warehouse_name' => [
                'required',
                'string',
                Rule::unique('berg_api_warehouses', 'warehouse_name')
                    ->where('berg_api_id', $this->bergApi->id)
            ],
            'supplier_warehouse_id' => [
                'required',
                'uuid',
                'exists:supplier_warehouses,id',
                Rule::unique('berg_api_warehouses', 'supplier_warehouse_id')
                    ->where('berg_api_id', $this->bergApi->id)
            ]
        ];
    }

    #[Computed]
    public function warehouses(): array|LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->bergApi
            ->warehouses()
            ->paginate();
    }

    public function destroy($id): void
    {
        $warehouse = $this->bergApi->warehouses()->find($id);
        $warehouse->delete();
    }

    public function store(): void
    {
        $this->validate();

        $this->bergApi->warehouses()->create([
            'name' => $this->name,
            'warehouse_name' => $this->warehouse_name,
            'supplier_warehouse_id' => $this->supplier_warehouse_id
        ]);
    }

    #[On('delete-warehouse')]
    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-warehouse.berg-api-warehouse-index');
    }
}
