<?php

namespace App\Livewire\EmailSupplierWarehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\EmailSupplierWarehouse\EmailSupplierWarehousePostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\EmailSupplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;

class EmailSupplierWarehouseIndex extends BaseComponent
{
    use WithJsNotifications;

    public EmailSupplierWarehousePostForm $form;

    public EmailSupplier $emailSupplier;

    #[On('email-supplier-warehouse-delete')]
    public function mount(): void
    {
        $this->form->setEmailSupplier($this->emailSupplier);
    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->emailSupplier->email);

        $this->form->store();

        \Flux::modal('create-email-supplier-warehouse-' . $this->emailSupplier->getKey())->close();
    }

    public function update(): void
    {
        $this->dispatch('email-supplier-warehouse-update')->component(EmailSupplierWarehouseEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email-supplier-warehouse.email-supplier-warehouse-index');
    }
}
