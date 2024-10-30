<?php

namespace App\Livewire\SupplierWarehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\SupplierWarehouse\SupplierWarehousePostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class SupplierWarehouseIndex extends BaseComponent
{
    use WithPagination;

    public SupplierWarehousePostForm $form;

    public Supplier $supplier;

    #[Computed]
    public function warehouses()
    {
        return $this->supplier
            ->warehouses()
            ->paginate();
    }

    public function mount(): void
    {
        $this->form->setSupplier($this->supplier);
    }

    public function destroy($id): void
    {
        $supplierWarehouse = SupplierWarehouse::find($id);

        $this->authorizeForUser($this->user(), 'update', $this->supplier);

        $this->form->setSupplierWarehouse($supplierWarehouse);
        $this->form->destroy();
    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->supplier);

        $this->form->store();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.supplier-warehouse.supplier-warehouse-index');
    }
}
