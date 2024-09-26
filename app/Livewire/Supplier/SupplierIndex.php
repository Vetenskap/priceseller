<?php

namespace App\Livewire\Supplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Models\Supplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Title;

#[Title('Поставщики')]
class SupplierIndex extends BaseComponent
{
    public SupplierPostForm $form;

    public function store(): void
    {
        $this->authorize('create', Supplier::class);

        $this->form->store();
    }

    public function changeOpen(string $id): void
    {
        $supplier = Supplier::find($id);

        $this->authorize('update', $supplier);

        $supplier->update(['open' => ! $supplier->open]);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.supplier.supplier-index', [
            'suppliers' => auth()->user()->suppliers
        ]);
    }
}
