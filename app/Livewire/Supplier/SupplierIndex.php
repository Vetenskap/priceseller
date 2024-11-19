<?php

namespace App\Livewire\Supplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Поставщики')]
class SupplierIndex extends BaseComponent
{
    use WithPagination, WithSort;

    public SupplierPostForm $form;

    public function edit($id): void
    {
        $this->redirect(route('supplier.edit', ['supplier' => $id]));
    }

    public function destroy($id): void
    {
        $supplier = Supplier::findOrFail($id);

        $this->authorizeForUser($this->user(), 'delete', $supplier);

        $this->form->setSupplier($supplier);
        $this->form->destroy();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function suppliers(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->suppliers());

    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'create', Supplier::class);

        $this->form->store();

        \Flux::modal('create-supplier')->close();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('view-suppliers')) {
            abort(403);
        }

        return view('livewire.supplier.supplier-index');
    }
}
