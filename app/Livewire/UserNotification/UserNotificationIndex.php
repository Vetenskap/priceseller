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
    public $enabled_site = false;

    public $actionsIds = [];

    public function mount()
    {
        if ($this->currentUser()->userNotification()->exists()) {
            $this->enabled_telegram = $this->currentUser()->userNotification->enabled_telegram;
            $this->enabled_site = $this->currentUser()->userNotification->enabled_site;
            $this->userNotification = $this->currentUser()->userNotification;
            $this->actionsIds = $this->userNotification->actions()->where('enabled', true)->pluck('notification_action_id')->toArray();
        }

        if ($link = $this->currentUser()->telegramLinks()->first()) {
            $this->token = $link->token;
        }
    }

    public function update()
    {
        if (!$this->userNotification) {
            $this->userNotification = $this->currentUser()->userNotification()->create();
        }
        $this->userNotification->update($this->only(['enabled_telegram', 'enabled_site']));
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

        if ($link = $this->currentUser()->telegramLinks()->first()) {
            $this->addError('telegram_link', 'Вы уже создали ссылку. До создания новой: ' . now()->diff($link->expires_at)->format('%i:%s'));
            return;
        }

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
