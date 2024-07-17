<?php

namespace App\Livewire\Supplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Session;
class SupplierEdit extends BaseComponent
{
    use WithJsNotifications, WithFilters;

    public SupplierPostForm $form;

    public Supplier $supplier;

    #[Session('supplierEdit.{supplier.id}')]
    public $selectedTab = 'main';

    public function mount()
    {
        $this->form->setSupplier($this->supplier);
    }

    public function save()
    {
        $this->authorize('update', $this->supplier);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy()
    {
        $this->authorize('delete', $this->supplier);

        DB::transaction(function () {
            $this->supplier->delete();
        });
    }

    public function render()
    {
        $this->authorize('view', $this->supplier);

        return view('livewire.supplier.supplier-edit', [
            'priceItems' => $this->supplier->priceItems()->filters()->paginate(100)
        ]);
    }
}
