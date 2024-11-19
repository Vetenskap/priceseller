<?php

namespace App\Livewire\Email;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Livewire\Traits\WithSort;
use App\Models\Email;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function destroy($id): void
    {
        $this->form->setEmail(Email::findOrFail($id));
        $this->form->destroy();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function emails(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->emails());

    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'create', Email::class);

        $this->form->store();

        \Flux::modal('create-email')->close();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('view-emails')) {
            abort(403);
        }

        return view('livewire.email.email-index');
    }
}
