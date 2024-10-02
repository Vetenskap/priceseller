<?php

namespace App\Livewire\Supplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Поставщики')]
class SupplierIndex extends BaseComponent
{
    use WithJsNotifications, WithPagination;

    public SupplierPostForm $form;

    public $sortBy = 'name';
    public $sortDirection = 'desc';

    public $dirtySuppliers = [];

    public function mount(): void
    {
        $this->dirtySuppliers = auth()->user()->suppliers->pluck(null, 'id')->toArray();
    }

    public function updatedDirtySuppliers(): void
    {
        collect($this->dirtySuppliers)->each(function ($supplier, $key) {
            $supplierModel = Supplier::findOrFail($key);
            $supplierModel->update($supplier);
        });
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function edit($id): void
    {
        $this->redirect(route('supplier.edit', ['supplier' => $id]));
    }

    public function destroy($id): void
    {
        $this->form->setSupplier(Supplier::findOrFail($id));
        $this->form->destroy();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function suppliers()
    {
        return auth()->user()
            ->suppliers()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function store(): void
    {
        $this->authorize('create', Supplier::class);

        $this->form->store();

        \Flux::modal('create-supplier')->close();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.supplier.supplier-index');
    }
}
