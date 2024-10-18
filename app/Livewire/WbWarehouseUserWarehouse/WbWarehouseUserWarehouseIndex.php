<?php

namespace App\Livewire\WbWarehouseUserWarehouse;

use App\Livewire\BaseComponent;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseUserWarehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
class WbWarehouseUserWarehouseIndex extends BaseComponent
{
    public WbWarehouse $warehouse;

    public $user_warehouse_id;

    #[Computed]
    public function userWarehouses(): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
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
//        $this->authorizeForUser($this->user(), 'create', OzonWarehouseUserWarehouse::class);

        $this->warehouse->userWarehouses()->create([
            'warehouse_id' => $this->user_warehouse_id
        ]);
    }

    public function destroy($id)
    {
        WbWarehouseUserWarehouse::findOrFail($id)->delete();
    }

    public function rules(): array
    {
        return [
            'user_warehouse_id' => [
                'required',
                'exists:warehouses,id',
                Rule::unique('wb_warehouse_user_warehouses', 'warehouse_id')
                    ->where('wb_warehouse_id', $this->warehouse->getKey())
            ]
        ];
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-warehouse-user-warehouse.wb-warehouse-user-warehouse-index');
    }
}
