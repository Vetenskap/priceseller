<?php

namespace App\Livewire\Email;

use App\Exceptions\ItemNotFoundException;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\Email\EmailPostForm;
use App\Models\Email;
use Livewire\Component;

class EmailShow extends Component
{
    public EmailPostForm $form;

    public Email $email;

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
        throw new ItemNotFoundException('товар не найден');

        $this->authorize('update', $this->email);

        $this->form->update();

        $this->js((new Toast("Почта: {$this->email->name}", 'Данные успешно обновлены'))->success());
    }

    public function render()
    {
        return view('livewire.email.email-show');
    }
}
