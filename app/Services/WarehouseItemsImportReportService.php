<?php

namespace App\Services;

use App\Events\ReportEvent;
use App\Events\NotificationEvent;
use App\Models\User;
use App\Models\WarehousesItemsImportReport;
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

            try {
                event(new ReportEvent($model->id));
            } catch (\Throwable $e) {
                report($e);
            }

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

            try {
                event(new ReportEvent($model->id));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }

//    public static function addBadItem(User $model, int $row, string $attribute, array $errors, array $values): bool
//    {
//        if ($report = static::get($model)) {
//
//            $report->badItems()->create([
//                'row' => $row,
//                'attribute' => $attribute,
//                'errors' => json_encode($errors),
//                'values' => json_encode($values),
//            ]);
//
//            return true;
//        } else {
//            return false;
//        }
//    }

    public static function success(User $model, int $correct, int $error, ?string $uuid = null): bool
    {
        if ($report = static::get($model)) {

            $report->correct = $correct;
            $report->error = $error;
            $report->status = 0;
            $report->message = 'Импорт завершен';

            if ($uuid) {
                $report->uuid = $uuid;
            }

            $report->save();


            try {
                event(new NotificationEvent($model->id, 'Объект: склады', 'Импорт завершен', 0));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }

    public static function error(User $model): bool
    {
        if ($report = static::get($model)) {
            $report->update([
                'status' => 1,
                'message' => 'Ошибка при импорте'
            ]);

            try {
                event(new NotificationEvent($model->id, 'Объект: склады', 'Ошибка при импорте', 1));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }

    public static function timeout()
    {
        WarehousesItemsImportReport::where('updated_at', '<', now()->subminutes(30))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (WarehousesItemsImportReport $report) {
                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время импорта'
                    ]);

                    try {
                        event(new NotificationEvent($report->user_id, 'Объект: склады', 'Вышло время импорта', 1));
                    } catch (\Throwable $e) {
                        report($e);
                    }

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
