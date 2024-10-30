<?php

namespace App\Livewire\OzonWarehouseSupplier;

use App\Livewire\BaseComponent;
use App\Models\OzonWarehouseSupplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class OzonWarehouseSupplierEdit extends BaseComponent
{
    public OzonWarehouseSupplier $supplier;

    public function destroy(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->supplier->warehouse->market);

        $this->supplier->delete();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-warehouse-supplier.ozon-warehouse-supplier-edit');
    }
}
