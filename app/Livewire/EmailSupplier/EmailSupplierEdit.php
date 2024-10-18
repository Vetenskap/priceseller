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
        $this->authorizeForUser($this->user(), 'view', $this->emailSupplier);

        return view('livewire.email-supplier.email-supplier-edit');
    }

    public function update(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->emailSupplier);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        $this->authorizeForUser($this->user(), 'delete', $this->emailSupplier);

        $this->form->destroy($this->emailSupplier->supplier_id);
    }

}
