<?php

namespace App\Notifications;

use App\Helpers\Helpers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $title, public string $message)
    {
        $this->queue = 'notifications';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->to($notifiable->userNotification->telegram_chat_id)
            ->line("*[".now()->setTimezone(Helpers::getUserTimeZone($notifiable))->format('Y-m-d H:i:s')."]*")
            ->line($this->title)
            ->line($this->message);
    }
}
