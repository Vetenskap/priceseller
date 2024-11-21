<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    $updates = \NotificationChannels\Telegram\TelegramUpdates::create()->get();

    if($updates['ok']) {

        foreach ($updates['result'] as $update) {
            if (isset($update['message']['text']) && isset($update['message']['chat'])) {
                $token = str_replace('/start ', '', $update['message']['text']);
                $chatId = $update['message']['chat']['id'];

                // Найти токен в базе
                $link = \App\Models\TelegramLink::where('token', $token)->first();

                if (!$link) {
                    continue;
                }

                // Проверить, не истёк ли токен
                if (\Illuminate\Support\Carbon::now()->greaterThan($link->expires_at)) {
                    continue;
                }

                // Связать chat_id с пользователем
                \App\Models\UserNotification::create([
                    'user_id' => $link->user_id,
                    'telegram_chat_id' => $chatId,
                ]);

                $link->user->notify(new \App\Notifications\TestTelegramNotification('Телеграм успешно связан'));

                // Удалить токен, чтобы он не использовался повторно
                $link->delete();
            }
        }
    }
})->everyMinute()->name('telegram-bot');

if (\Illuminate\Support\Facades\App::isProduction())
{
    Schedule::command('clean:telegram-links')->hourly();

    Schedule::call(function () {

        \App\Services\MarketsService::updateCommissionsInTime();
        \App\Services\BusinessLogicService::usersEmailsUnload();
        \App\Services\ReportService::checkTimeouts();

    })->everyMinute()->name('everyMinuteSchedule');

    Schedule::call(function () {

        \Illuminate\Support\Facades\Artisan::call('horizon:snapshot');
        \App\Services\UsersPermissionsService::closeMarkets();

    })->everyFiveMinutes()->name('everyFiveMinutesSchedule')->withoutOverlapping();

    Schedule::call(function () {

        \Illuminate\Support\Facades\Artisan::call('telescope:prune');
        \App\Services\PruneService::reports();
        \App\Services\UsersPermissionsService::sendNotifications();

    })->daily();
}
