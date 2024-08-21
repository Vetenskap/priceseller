<?php

namespace App\Livewire\Supplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class SupplierEdit extends BaseComponent
{
    use WithJsNotifications, WithFilters;

    public $backRoute = 'suppliers.index';

    public SupplierPostForm $form;

    public Supplier $supplier;

    public $page;

    public function mount($page = 'main'): void
    {
        $this->page = $page;
        $this->form->setSupplier($this->supplier);
    }

    public function update(): void
    {
        $this->authorize('update', $this->supplier);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        $this->authorize('delete', $this->supplier);

        $this->form->destroy();

        $this->redirectRoute($this->backRoute);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorize('view', $this->supplier);

        if ($this->page === 'main') {
            return view('livewire.supplier.pages.supplier-edit-main-page');
        } else if ($this->page === 'price') {
            return view('livewire.supplier.pages.supplier-edit-price-page', [
                'priceItems' => $this->supplier->priceItems
            ]);
        }

        abort(404);
    }
}
