<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class Notification extends BaseComponent
{
    public Collection $notifications;

    public function mount(): void
    {
        $this->notifications = $this->getNotifications();
    }

    public function getNotifications(): Collection
    {
        return $this->currentUser()->notifications()->limit(15)->get();
    }

    public function clear(): void
    {
        $this->notifications->each(fn (\App\Models\Notification $notification) => $notification->delete());
        $this->notifications = $this->getNotifications();
    }

    public function getListeners(): array
    {
        return [
            'echo:notification.' . $this->currentUser()->id . ',.notify' => 'notification',
        ];
    }

    public function notification($event): void
    {
        $this->notifications = $this->getNotifications();

        switch ($event['status']) {
            case 0:
                \Flux::toast($event['message'], $event['title'], variant: 'success');
                break;
            case 1:
                \Flux::toast($event['message'], $event['title'], variant: 'danger');
                break;
            case 2:
                \Flux::toast($event['message'], $event['title']);
                break;
        }
    }

    public function render(): Application|Factory|View|\Illuminate\View\View
    {
        return view('livewire.notification');
    }
}
