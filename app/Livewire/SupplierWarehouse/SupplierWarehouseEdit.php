<?php

namespace App\Livewire\SupplierWarehouse;

use App\Livewire\Forms\SupplierWarehouse\SupplierWarehousePostForm;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class SupplierWarehouseEdit extends Component
{
    public SupplierWarehousePostForm $form;

    public Supplier $supplier;

    public SupplierWarehouse $warehouse;

    public function mount(): void
    {
        $this->form->setSupplier($this->supplier);
        $this->form->setSupplierWarehouse($this->warehouse);
    }

    #[On('supplier-warehouse-update')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('supplier-warehouse-delete')->component(SupplierWarehouseIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.supplier-warehouse.supplier-warehouse-edit');
    }
}
