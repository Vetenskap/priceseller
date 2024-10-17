<?php

namespace App\Livewire;

class NotificationDiv extends BaseComponent
{
    public function mount()
    {
        $date = session('notification.div');
        if ($date && ($date < now()->toDateTimeString())) {
            session()->forget('notification.div');
        }
    }


    public function close()
    {
        session()->put('notification.div', now()->addDay()->startOfDay()->toDateTimeString());
    }

    public function render()
    {
        return view('livewire.notification-div');
    }
}
