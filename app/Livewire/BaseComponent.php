<?php

namespace App\Livewire;

use App\Livewire\Components\Toast;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class BaseComponent extends Component
{
    use WithJsNotifications;

    public function getListeners()
    {
        return [
            'echo:notification.' . $this->currentUser()->id . ',.notify' => 'notification',
//            'echo:report.' . $this->currentUser()->id . ',.event' => '$refresh'
        ];
    }

    public function checkTtlJob($lockKey, $class): bool
    {
        $ttl = Redis::ttl('laravel_unique_job:' . $class . ':' . $lockKey);

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

    public function back()
    {
        return redirect()->to(url()->previous());
    }

    public function isEmployee(): bool
    {
        return Auth::guard('employee')->check();  // Проверяем, сотрудник ли это
    }

    public function currentUser(): User
    {
        if ($this->isEmployee()) {
            return Auth::guard('employee')->user()->user;  // Вернуть владельца сотрудника
        }

        return Auth::user();
    }

    public function user(): User|Employee
    {
        if ($this->isEmployee()) {
            return Auth::guard('employee')->user();
        }

        return Auth::user();
    }
}
