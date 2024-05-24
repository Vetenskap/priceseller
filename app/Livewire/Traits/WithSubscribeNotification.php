<?php

namespace App\Livewire\Traits;

use App\Livewire\Components\Toast;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;

trait WithSubscribeNotification
{
    public function getListeners()
    {
        return [
            'echo:notification.' . auth()->user()->id . ',.notify' => 'notification'
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
