<?php

namespace App\Livewire\UserNotification;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSaveButton;
use App\Models\UserNotification;
use Illuminate\Support\Str;

class UserNotificationIndex extends BaseComponent
{
    use WithSaveButton;

    public $token = null;

    public ?UserNotification $userNotification = null;
    public $enabled_telegram = false;

    public $actionsIds = [];

    public function mount()
    {
        if ($this->currentUser()->userNotification()->exists()) {
            $this->enabled_telegram = $this->currentUser()->userNotification->enabled_telegram;
            $this->userNotification = $this->currentUser()->userNotification;
            $this->actionsIds = $this->userNotification->actions()->where('enabled', true)->pluck('notification_action_id')->toArray();
        }
    }

    public function update()
    {
        $this->userNotification->update($this->only('enabled_telegram'));
        foreach ($this->actionsIds as $actionId) {
            $this->userNotification->actions()->updateOrCreate([
                'notification_action_id' => $actionId
            ], [
                'notification_action_id' => $actionId,
                'enabled' => true
            ]);
        }
        $this->userNotification->actions()->whereNotIn('notification_action_id', $this->actionsIds)->update(['enabled' => false]);
        $this->hideSaveButton();
        \Flux::toast('Сохранено');
    }

    public function createLink()
    {
        $token = Str::uuid()->toString();

        $this->currentUser()->telegramLinks()->create([
            'token' => $token,
            'expires_at' => now()->addMinutes(15),
        ]);

        $this->token = $token;
    }

    public function render()
    {
        return view('livewire.user-notification.user-notification-index');
    }
}
