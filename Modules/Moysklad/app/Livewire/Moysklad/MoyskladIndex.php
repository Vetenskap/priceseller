<?php

namespace Modules\Moysklad\Livewire\Moysklad;

use App\Livewire\Traits\WithJsNotifications;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Moysklad\Services\MoyskladService;
use MoyskladPostForm;

class MoyskladIndex extends Component
{
    use WithJsNotifications, WithFileUploads;

    public MoyskladPostForm $form;

    #[Url]
    public $page = 'main';

    public $file;

    public function save()
    {
        if ($this->form->moysklad) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        $this->addSuccessSaveNotification();
    }

    public function mount()
    {
        $this->form->setMoysklad(auth()->user()->moysklad);
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad.moysklad-index');
    }
}
