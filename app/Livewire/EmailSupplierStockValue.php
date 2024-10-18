<?php

namespace App\Livewire;

use App\Livewire\Forms\EmailSupplierStockValuePostForm;
use App\Livewire\Traits\WithJsNotifications;

class EmailSupplierStockValue extends BaseComponent
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
        $this->authorizeForUser($this->user(), 'update', $this->stockValue);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        $this->authorizeForUser($this->user(), 'view', $this->stockValue);

        return view('livewire.email-supplier-stock-value');
    }
}
