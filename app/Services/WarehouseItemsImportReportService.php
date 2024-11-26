<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Models\User;
use App\Models\WarehousesItemsImportReport;
use App\Notifications\UserNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class WarehouseItemsImportReportService
{
    public static function get(User $model): ?WarehousesItemsImportReport
    {
        return $model->warehousesItemsImportReports()->where('status', 2)->first();
    }

    public static function newOrFail(User $model, string $uuid): bool
    {
        if (static::get($model)) {
            return false;
        } else {
            $model->warehousesItemsImportReports()->create([
                'status' => 2,
                'message' => 'В процессе',
                'uuid' => $uuid,
            ]);

            return true;
        }
    }

    public static function flush(User $model, int $correct, int $error): bool
    {
        if ($report = static::get($model)) {

            $report->update([
                'correct' => $correct,
                'error' => $error,
            ]);

            return true;
        } else {
            return false;
        }
    }

    public static function success(User $user, int $correct, int $error, ?string $uuid = null): bool
    {
        if ($report = static::get($user)) {

            $report->correct = $correct;
            $report->error = $error;
            $report->status = 0;
            $report->message = 'Импорт завершен';

            if ($uuid) {
                $report->uuid = $uuid;
            }

            $report->save();

            NotificationService::send($user->id, 'Склады', 'Импорт завершен', 0, null, 'import');

            return true;
        } else {
            return false;
        }
    }

    public static function error(User $user): bool
    {
        if ($report = static::get($user)) {
            $report->update([
                'status' => 1,
                'message' => 'Ошибка при импорте'
            ]);

            NotificationService::send($user->id, 'Склады', 'Ошибка при импорте', 1, null, 'import');

            return true;
        } else {
            return false;
        }
    }

    public static function timeout(): void
    {
        WarehousesItemsImportReport::where('updated_at', '<', now()->subminutes(30))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (WarehousesItemsImportReport $report) {
                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время импорта'
                    ]);

                    NotificationService::send($report->user_id, 'Склады', 'Вышло время импорта', 1, null, 'import');

                });
            });
    }

    public static function prune()
    {
        $totalDeleted = 0;

        do {
            $reports = WarehousesItemsImportReport::where('updated_at', '<', now()->subWeek())->limit(100)->get();

            foreach ($reports as $report) {

                Storage::delete('users/warehouses/' . "{$report->uuid}.xlsx");

                $report->delete();
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
