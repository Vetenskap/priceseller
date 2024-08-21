<?php

namespace App\Livewire\Email;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Models\Email;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class EmailIndex extends BaseComponent
{
    public EmailPostForm $form;

    public function store(): void
    {
        $this->authorize('create', Email::class);

        $this->form->store();
    }

    public function changeOpen(string $id): void
    {
        $email = Email::find($id);

        $this->authorize('update', $email);

        $email->open = !$email->open;
        $email->save();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email.email-index', [
            'emails' => auth()->user()->emails
        ]);
    }
}
