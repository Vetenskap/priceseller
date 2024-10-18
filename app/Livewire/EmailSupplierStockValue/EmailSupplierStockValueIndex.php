<?php

namespace App\Livewire\EmailSupplierStockValue;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\EmailSupplierStockValuePostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\EmailSupplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;

class EmailSupplierStockValueIndex extends BaseComponent
{
    use WithJsNotifications;

    public EmailSupplierStockValuePostForm $form;

    public EmailSupplier $emailSupplier;

    #[On('email-supplier-stock-value-deleted')]
    public function mount(): void
    {
        $this->form->setEmailSupplier($this->emailSupplier);
    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->emailSupplier->email);

        $this->form->store();

        \Flux::modal('create-email-supplier-stock-value-'. $this->emailSupplier->getKey())->close();
    }

    public function update(): void
    {
        $this->dispatch('email-supplier-stock-value-updated')->component(EmailSupplierStockValueEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email-supplier-stock-value.email-supplier-stock-value-index');
    }
}
