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

    public function mount(): void
    {
        $this->user = $this->currentUser();

        $this->baseSettings = $this->user->baseSettings;
        $this->enabled_use_buy_price_reserve = $this->baseSettings ? (bool) $this->baseSettings->enabled_use_buy_price_reserve : false;
    }

    public function save(): void
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
        if (!$this->currentUser()->can('view-basesettings')) {
            abort(403);
        }

        return view('livewire.base-settings.base-settings-index');
    }
}
