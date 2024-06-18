<?php

namespace App\Livewire\Moysklad;

use App\Livewire\Forms\MoyskladPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Moysklad;
use App\Services\MoyskladService;
use Livewire\Component;

class MoyskladIndex extends Component
{
    use WithJsNotifications;

    public MoyskladPostForm $form;

    public $selectedTab;

    public $apiWarehouses;

    public function mount()
    {
        $this->form->setMoysklad(auth()->user()->moysklad);
        if ($this->form->moysklad) {
            $service = new MoyskladService($this->form->moysklad);
            $service->setClient();
            $this->apiWarehouses = $service->getWarehouses();
        }
    }

    public function save()
    {
        if ($this->form->moysklad) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        return view('livewire.moysklad.moysklad-index');
    }
}
