<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Models\User;
use App\Notifications\UserNotification;

class NotificationService
{
    public static function send(int $userId, string $title, string $message, int $status, ?string $href, string $action): void
    {
        try {
            $user = User::findOrFail($userId);

            if ($user->userNotificationActionEnabled($action)) {
                if ($user->userNotification?->enabled_site) {
                    event(new NotificationEvent($userId, $title, $message, $status, $href));
                }

                if ($user->userNotification->enabled_telegram) {
                    $user->notify(new UserNotification($title, $message));
                }
            }

        } catch (\Throwable $e) {
            report($e);
        }
    }
}
