<?php

namespace App\Livewire\SupplierWarehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\SupplierWarehouse\SupplierWarehousePostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;

class SupplierWarehouseIndex extends BaseComponent
{
    use WithJsNotifications;

    public SupplierWarehousePostForm $form;

    public Supplier $supplier;

    #[On('supplier-warehouse-delete')]
    public function mount(): void
    {
        $this->form->setSupplier($this->supplier);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function update(): void
    {
        $this->dispatch('supplier-warehouse-update')->component(SupplierWarehouseEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.supplier-warehouse.supplier-warehouse-index');
    }
}
