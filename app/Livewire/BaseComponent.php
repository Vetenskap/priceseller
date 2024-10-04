<?php

namespace App\Livewire;

use App\Livewire\Components\Toast;
use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class BaseComponent extends Component
{
    use WithJsNotifications;

    public function getListeners()
    {
        return [
            'echo:notification.' . auth()->user()->id . ',.notify' => 'notification',
            'echo:report.' . auth()->user()->id . ',.event' => '$refresh'
        ];
    }

    public function checkTtlJob($lockKey, $class): bool
    {
        $ttl = Redis::ttl('laravel_unique_job:'.$class.':'.$lockKey);

        if ($ttl > 0) {
            \Flux::toast('Задание уже выполняется. Осталось секунд до окончания блокировки: ' . $ttl);
            return false;
        } else {
            $this->addJobNotification();
            return true;
        }
    }

    public function notification($event)
    {
        switch ($event['status']) {
            case 0:
                \Flux::toast($event['message'], $event['title']);
                break;
            case 1:
                \Flux::toast($event['message'], $event['title']);
                break;
            case 2:
                \Flux::toast($event['message'], $event['title']);
                break;
        }
    }

    public function back(): void
    {
        $this->redirectRoute($this->backRoute);
    }
}
