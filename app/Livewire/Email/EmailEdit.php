<?php

namespace App\Livewire\Email;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSaveButton;
use App\Models\Email;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

#[Title('Почта')]
class EmailEdit extends BaseComponent
{
    use WithJsNotifications, WithSaveButton;

    public EmailPostForm $form;

    public Email $email;

    // TODO: refactor email suppliers
    public $emailSuppliers;

    public $backRoute = 'emails.index';

    public function mount(): void
    {
        $this->form->setEmail($this->email);
    }

    public function destroy(): void
    {
        $this->authorizeForUser($this->user(), 'delete', $this->email);

        $this->form->destroy();

        $this->redirectRoute($this->backRoute);
    }

    public function update(): void
    {

        $this->authorizeForUser($this->user(), 'update', $this->email);

        $this->form->update();

        $this->addSuccessSaveNotification();
        $this->hideSaveButton();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorizeForUser($this->user(), 'view', $this->email);

        return view('livewire.email.email-edit');
    }

}
