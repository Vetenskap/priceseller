<?php

namespace App\Livewire;

use App\Livewire\Components\Toast;
use Livewire\Component;

class BaseComponent extends Component
{
    public function getListeners()
    {
        return [
            'echo:notification.' . auth()->user()->id . ',.notify' => 'notification',
            'echo:report.' . auth()->user()->id . ',.event' => '$refresh'
        ];
    }

    public function notification($event)
    {
        switch ($event['status']) {
            case 0:
                $this->js((new Toast($event['title'], $event['message']))->success());
                break;
            case 1:
                $this->js((new Toast($event['title'], $event['message']))->danger());
                break;
            case 2:
                $this->js((new Toast($event['title'], $event['message']))->info());
                break;
        }
    }
}
