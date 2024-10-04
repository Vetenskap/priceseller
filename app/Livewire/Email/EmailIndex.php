<?php

namespace App\Livewire\Email;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\Email;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Почта')]
class EmailIndex extends BaseComponent
{
    use WithPagination, WithSort;

    public EmailPostForm $form;

    public $dirtyEmails = [];

    public function mount(): void
    {
        $this->dirtyEmails = auth()->user()->emails->pluck(null, 'id')->toArray();
    }

    public function updatedDirtyEmails(): void
    {
        collect($this->dirtyEmails)->each(function ($email, $key) {
            $emailModel = Email::findOrFail($key);
            $emailModel->update($email);
        });
    }

    public function destroy($id): void
    {
        $this->form->setEmail(Email::findOrFail($id));
        $this->form->destroy();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function emails()
    {
        return auth()->user()
            ->emails()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function store(): void
    {
        $this->authorize('create', Email::class);

        $this->form->store();

        \Flux::modal('create-email')->close();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email.email-index');
    }
}
