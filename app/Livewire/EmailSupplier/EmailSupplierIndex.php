<?php

namespace App\Livewire\EmailSupplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\EmailSupplier\EmailSupplierPostForm;
use App\Models\Email;
use App\Models\EmailSupplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class EmailSupplierIndex extends BaseComponent
{
    public EmailSupplierPostForm $form;

    public Email $email;

    public function mount(): void
    {
        $this->form->setMainEmal($this->email);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email-supplier.email-supplier-index');
    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->email);

        $this->form->store();

        \Flux::modal('create-email-supplier')->close();
    }

    public function delete(string $id): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->email);

        $this->form->destroy($id);
    }
}
