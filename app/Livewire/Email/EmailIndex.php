<?php

namespace App\Livewire\Email;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Email;
use Livewire\Component;

class EmailIndex extends Component
{
    use WithSubscribeNotification;

    public EmailPostForm $form;

    public $showCreateBlock = false;

    public function add()
    {
        $this->showCreateBlock = ! $this->showCreateBlock;
    }

    public function store()
    {
        $this->authorize('create', Email::class);

        $this->form->store();

        $this->reset('showCreateBlock');
    }

    public function changeOpen($email)
    {
        $email = Email::find($email['id']);

        $this->authorize('update', $email);

        $email->open = !$email->open;
        $email->save();
    }

    public function destroy($email)
    {
        $email = Email::find($email['id']);

        $this->authorize('delete', $email);

        $email->delete();
    }

    public function render()
    {
        return view('livewire.email.email-index', [
            'emails' => Email::where('user_id', \auth()->user()->id)->get()
        ]);
    }
}
