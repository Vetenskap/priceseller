<?php

namespace App\Livewire\BaseSettings;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\User;
use App\Models\UserBaseSetting;
use Livewire\Component;

class BaseSettingsIndex extends BaseComponent
{
    use WithJsNotifications;

    public User $user;
    public $baseSettings;

    public $enabled_use_buy_price_reserve = false;

    public function mount()
    {
        $this->user = auth()->user();

        $this->baseSettings = $this->user->baseSettings;
        $this->enabled_use_buy_price_reserve = $this->baseSettings->enabled_use_buy_price_reserve ?? false;
    }

    public function save()
    {
        if ($this->baseSettings) {
            $baseSettings = $this->baseSettings;
        } else {
            $baseSettings = new UserBaseSetting([
                'user_id' => $this->user->id
            ]);
        }

        $baseSettings->enabled_use_buy_price_reserve = $this->enabled_use_buy_price_reserve;
        $baseSettings->save();

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        return view('livewire.base-settings.base-settings-index');
    }
}