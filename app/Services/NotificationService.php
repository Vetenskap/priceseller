<?php

namespace App\Services;

use App\Contracts\NotificationContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Events\NotificationEvent;
use App\Models\User;
use App\Notifications\UserNotification;

class NotificationService implements NotificationContract
{
    public function send(int $userId, string $title, string $message, ReportStatus $status, TaskTypes $type, ?string $href): bool
    {
        try {
            $user = User::findOrFail($userId);

            if ($user->userNotificationActionEnabled($type)) {
                if ($user->userNotification?->enabled_site) {
                    event(new NotificationEvent($userId, $title, $message, $status, $href));
                }

                if ($user->userNotification->enabled_telegram) {
                    $user->notify(new UserNotification($title, $message));
                }
            }

        } catch (\Throwable $e) {
            report($e);
            return false;
        }

        return true;
    }
}
