<?php

namespace App\Livewire\Email;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Models\Email;
use Livewire\Component;

class EmailIndex extends Component
{
    public EmailPostForm $form;

    public $emails;

    public $showCreateBlock = false;

    public function mount()
    {
        $this->emails = Email::where('user_id', \auth()->user()->id)->get();
    }

    public function add()
    {
        $this->showCreateBlock = ! $this->showCreateBlock;
    }

    public function store()
    {
        $this->authorize('create', Email::class);

        $email = $this->form->store();

        $this->emails->add($email);

        $this->reset('showCreateBlock');
    }

    public function changeOpen(Email $email)
    {
        $this->authorize('update', $email);

        $email->update(['open' => ! $email->open]);

        $this->js((new Toast("Почта: {$email->name}", 'Данные успешно применены'))->success());
    }

    public function destroy(Email $email)
    {
        $this->authorize('delete', $email);

        $email->delete();
    }

    public function render()
    {
        return view('livewire.email.email-index');
    }
}
