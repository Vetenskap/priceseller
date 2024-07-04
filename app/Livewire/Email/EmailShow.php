<?php

namespace App\Livewire\Email;

use App\Exceptions\ItemNotFoundException;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Email;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

class EmailShow extends Component
{
    use WithJsNotifications, WithSubscribeNotification;

    public EmailPostForm $form;

    public Email $email;

    public $emailSuppliers;

    public function mount()
    {
        $this->form->setEmail($this->email);
    }

    public function destroy()
    {
        $this->authorize('delete', $this->email);

        $this->email->delete();

        $this->redirectRoute('emails');
    }

    public function save()
    {

        $this->authorize('update', $this->email);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        $this->authorize('view', $this->email);

        return view('livewire.email.email-show');
    }

}
