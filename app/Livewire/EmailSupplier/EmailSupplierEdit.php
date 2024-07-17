<?php

namespace App\Livewire\EmailSupplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\EmailSupplierPostForm;
use App\Models\EmailSupplierStockValue;

class EmailSupplierEdit extends BaseComponent
{

    public EmailSupplierPostForm $form;

    public $selectedTab = 'main';

    public $emailSupplierId;
    public \App\Models\EmailSupplier $emailSupplier;

    public function mount()
    {
        $this->emailSupplier = \App\Models\EmailSupplier::find($this->emailSupplierId);
        $this->form->setEmailSupplier($this->emailSupplier);
    }

    public function render()
    {
        $this->authorize('view', $this->emailSupplier);

        return view('livewire.email-supplier.email-supplier-edit');
    }

    public function save()
    {
        $this->authorize('update', $this->emailSupplier);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function addEmailSupplierStockValue()
    {
        $this->authorize('create', EmailSupplierStockValue::class);

        $this->emailSupplier->stockValues()->create();
    }

    public function deleteEmailSupplierStockValue($stockValue)
    {
        $stockValue = $this->emailSupplier->stockValues()->find($stockValue['id']);

        $this->authorize('delete', $stockValue);

        $stockValue->delete();
    }
}
