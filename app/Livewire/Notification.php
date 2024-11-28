<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class Notification extends BaseComponent
{
    public Collection $notifications;
    public int $offset = 0;
    public bool $hasMore = true;

    public function mount(): void
    {
        $this->notifications = collect();
        $this->loadMore();
    }

    public function getNotifications(int $offset, int $limit = 15): Collection
    {
        return $this->currentUser()
            ->notifications()
            ->offset($offset)
            ->limit($limit)
            ->orderByDesc('created_at')
            ->get();
    }

    public function loadMore(): void
    {
        if (!$this->hasMore) {
            return; // Предотвращает лишние запросы, если больше данных нет
        }

        $newNotifications = $this->getNotifications($this->offset);
        $this->notifications = $this->notifications->merge($newNotifications);
        $this->offset += $newNotifications->count();

        // Проверяем, осталось ли больше данных
        $this->hasMore = $newNotifications->count() === 15;
    }

    public function clear(): void
    {
        $this->currentUser()->notifications()->delete();
        $this->notifications = collect();
        $this->offset = 0;
        $this->hasMore = false;
    }

    public function getListeners(): array
    {
        return [
            'echo:notification.' . $this->currentUser()->id . ',.notify' => 'notification',
        ];
    }

    public function notification($event): void
    {
        $this->notifications = $this->getNotifications($this->offset);

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
