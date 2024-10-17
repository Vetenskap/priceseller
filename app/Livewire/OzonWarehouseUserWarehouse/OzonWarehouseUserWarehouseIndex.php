<?php

namespace App\Livewire\OzonWarehouseUserWarehouse;

use App\Livewire\BaseComponent;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseUserWarehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use LaravelIdea\Helper\App\Models\_IH_OzonWarehouseUserWarehouse_C;
use Livewire\Attributes\Computed;

class OzonWarehouseUserWarehouseIndex extends BaseComponent
{
    public OzonWarehouse $warehouse;

    public $user_warehouse_id;

    #[Computed]
    public function userWarehouses(): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array|_IH_OzonWarehouseUserWarehouse_C
    {
        return $this->warehouse
            ->userWarehouses()
            ->with('warehouse')
            ->paginate();
    }

    public function store(): void
    {
        $this->validate();

        // TODO: add authorization
//        $this->authorize('create', OzonWarehouseUserWarehouse::class);

        $this->warehouse->userWarehouses()->create([
            'warehouse_id' => $this->user_warehouse_id
        ]);
    }

    public function destroy($id)
    {
        OzonWarehouseUserWarehouse::findOrFail($id)->delete();
    }

    public function rules(): array
    {
        return [
            'user_warehouse_id' => [
                'required',
                'exists:warehouses,id',
                Rule::unique('ozon_warehouse_user_warehouses', 'warehouse_id')
                    ->where('ozon_warehouse_id', $this->warehouse->getKey())
            ]
        ];
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-warehouse-user-warehouse.ozon-warehouse-user-warehouse-index');
    }
}
