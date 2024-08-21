<?php

namespace App\Livewire\EmailSupplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\EmailSupplier\EmailSupplierPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\EmailSupplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class EmailSupplierEdit extends BaseComponent
{
    use WithJsNotifications;

    public EmailSupplierPostForm $form;

    public $emailSupplierId;
    public EmailSupplier $emailSupplier;

    public function mount(): void
    {
        $this->emailSupplier = EmailSupplier::find($this->emailSupplierId);
        $this->form->setEmailSupplier($this->emailSupplier);
        $this->form->setMainEmal($this->emailSupplier->mainEmail);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorize('view', $this->emailSupplier);

        return view('livewire.email-supplier.email-supplier-edit');
    }

    public function update(): void
    {
        $this->authorize('update', $this->emailSupplier);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        $this->authorize('delete', $this->emailSupplier);

        $this->form->destroy($this->emailSupplier->supplier_id);
    }

//    public function addEmailSupplierStockValue()
//    {
//        $this->authorize('create', EmailSupplierStockValue::class);
//
//        $this->emailSupplier->stockValues()->create();
//    }
//
//    public function deleteEmailSupplierStockValue($stockValue)
//    {
//        $stockValue = $this->emailSupplier->stockValues()->find($stockValue['id']);
//
//        $this->authorize('delete', $stockValue);
//
//        $stockValue->delete();
//    }
}
