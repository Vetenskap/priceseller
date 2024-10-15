<?php

namespace App\Livewire\WarehouseItemStock;

use App\Models\ItemWarehouseStock;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Validate;
use Livewire\Component;

class WarehouseItemStockEdit extends Component
{
    public ItemWarehouseStock $stock;

    #[Validate]
    public $count;

    public function rules(): array
    {
        return ['count' => ['required', 'integer', 'min:0']];
    }

    public function updatedCount(): void
    {
        $this->validate();

        $this->stock->update(['stock' => $this->count]);
    }

    public function mount(): void
    {
        $this->count = $this->stock->stock;
    }

    public function destroy(): void
    {
        // TODO: add auth

        $this->stock->delete();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.warehouse-item-stock.warehouse-item-stock-edit');
    }
}
