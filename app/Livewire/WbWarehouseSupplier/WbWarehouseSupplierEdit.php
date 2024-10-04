<?php

namespace App\Livewire\WbWarehouseSupplier;

use App\Livewire\BaseComponent;
use App\Models\WbWarehouseSupplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class WbWarehouseSupplierEdit extends BaseComponent
{
    public WbWarehouseSupplier $supplier;

    public function destroy(): void
    {
        // TODO: add authorization
//        $this->authorize('delete', $this->supplier);

        $this->supplier->delete();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-warehouse-supplier.wb-warehouse-supplier-edit');
    }
}
