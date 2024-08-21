<?php

namespace App\Livewire\Email;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Email;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class EmailEdit extends BaseComponent
{
    use WithJsNotifications;

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
        $this->authorize('delete', $this->email);

        $this->form->destroy();

        $this->redirectRoute($this->backRoute);
    }

    public function update(): void
    {

        $this->authorize('update', $this->email);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorize('view', $this->email);

        return view('livewire.email.email-edit');
    }

}
