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
            $this->js((new Toast('Уведомление', 'Задание уже выполняется. Осталось секунд до окончания блокировки: ' . $ttl))->warning());
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

    public function back(): void
    {
        $this->redirectRoute($this->backRoute);
    }
}
