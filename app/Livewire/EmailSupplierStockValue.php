<?php

namespace App\Livewire;

use App\Livewire\Forms\EmailSupplierStockValuePostForm;
use App\Livewire\Traits\WithJsNotifications;
use Livewire\Component;

class EmailSupplierStockValue extends Component
{
    use WithJsNotifications;

    public EmailSupplierStockValuePostForm $form;

    public \App\Models\EmailSupplierStockValue $stockValue;

    public function mount()
    {
        $this->form->setStockValue($this->stockValue);
    }

    public function save()
    {
        $this->authorize('update', $this->stockValue);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        $this->authorize('view', $this->stockValue);

        return view('livewire.email-supplier-stock-value');
    }
}
