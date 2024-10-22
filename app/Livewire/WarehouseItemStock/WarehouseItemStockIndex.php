<?php

namespace App\Livewire\WarehouseItemStock;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithItemsFind;
use App\Models\ItemWarehouseStock;
use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;
use LaravelIdea\Helper\App\Models\_IH_ItemWarehouseStock_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

class WarehouseItemStockIndex extends BaseComponent
{
    use WithItemsFind, WithPagination;

    public Warehouse $warehouse;

    public $changeStock;

    #[Validate]
    public $item_id;

    #[Validate]
    public $stock;

    #[Computed]
    public function stocks(): _IH_ItemWarehouseStock_C|LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        return $this->warehouse
            ->stocks()
            ->paginate();
    }

    public function store(): void
    {
        $this->validate();

        $this->authorizeForUser($this->user(), 'update', $this->warehouse);

        $this->warehouse->stocks()->create([
            'item_id' => $this->item_id,
            'stock' => $this->stock
        ]);
    }

    public function destroy($id): void
    {
        $stock = ItemWarehouseStock::find($id);

        $this->authorizeForUser($this->user(), 'update', $this->warehouse);

        $stock->delete();
    }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'uuid', 'exists:items,id', Rule::unique('item_warehouse_stocks', 'item_id')->where('warehouse_id', $this->warehouse->id)],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.warehouse-item-stock.warehouse-item-stock-index');
    }
}
