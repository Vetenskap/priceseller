<?php

namespace App\Livewire\Supplier;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;

class SupplierEdit extends Component
{
    use WithJsNotifications, WithFilters, WithSubscribeNotification;

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

        $this->supplier->delete();
    }

    public function render()
    {
        $this->authorize('view', $this->supplier);

        return view('livewire.supplier.supplier-edit', [
            'priceItems' => $this->supplier->priceItems()->filters()->paginate(100)
        ]);
    }
}
