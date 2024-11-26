<?php

use Illuminate\Support\Facades\Schedule;

if (\Illuminate\Support\Facades\App::isProduction()) {
    Schedule::call(function () {
        $updates = \NotificationChannels\Telegram\TelegramUpdates::create()->get();

        if ($updates['ok']) {

            foreach ($updates['result'] as $update) {
                if (isset($update['message']['text']) && isset($update['message']['chat'])) {
                    $token = str_replace('/start ', '', $update['message']['text']);
                    $chatId = $update['message']['chat']['id'];

                    $link = \App\Models\TelegramLink::where('token', $token)->first();

                    if (!$link) {
                        continue;
                    }

                    if (\Illuminate\Support\Carbon::now()->greaterThan($link->expires_at)) {
                        continue;
                    }

                    if ($userNotification = $link->user->userNotification()->first()) {
                        $userNotification->update([
                            'telegram_chat_id' => $chatId,
                        ]);

                        $link->user->notify(new \App\Notifications\UserNotification('Телеграм', 'Успешно связан!'));
                    } else {
                        \App\Models\UserNotification::create([
                            'user_id' => $link->user_id,
                            'telegram_chat_id' => $chatId,
                        ]);

                        $link->user->notify(new \App\Notifications\UserNotification('Телеграм', 'Успешно связан!'));
                    }

                    $link->delete();
                }
            }
        }
    })->everyMinute()->name('telegram-bot');

    Schedule::command('clean:telegram-links')->hourly();

    Schedule::call(function () {

        \App\Services\MarketsService::updateCommissionsInTime();
        \App\Services\BusinessLogicService::usersEmailsUnload();
        \App\Services\ReportService::checkTimeouts();

    })->everyMinute()->name('everyMinuteSchedule');

    Schedule::call(function () {

        \Illuminate\Support\Facades\Artisan::call('horizon:snapshot');

    })->everyFiveMinutes()->name('everyFiveMinutesSchedule')->withoutOverlapping();

    Schedule::call(function () {

        \Illuminate\Support\Facades\Artisan::call('telescope:prune');
        \App\Services\PruneService::reports();
        \App\Services\UsersPermissionsService::sendNotifications();

    })->daily();
}
