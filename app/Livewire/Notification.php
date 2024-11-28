<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class Notification extends BaseComponent
{
    public Collection $notifications;
    public $offset = 0;

    public function mount(): void
    {
        $this->notifications = $this->getNotifications();
    }

    public function getNotifications(): Collection
    {
        return $this->currentUser()->notifications()->offset($this->offset)->limit(15)->orderBy('created_at')->get();
    }

    public function existsMore(): bool
    {
        return $this->currentUser()->notifications()->offset($this->offset + 15)->limit(15)->orderBy('created_at')->count();
    }

    public function loadMore(): void
    {
        if ($this->existsMore()) {
            $this->offset += 15;
            $this->notifications = $this->notifications->merge($this->getNotifications());
        }
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
