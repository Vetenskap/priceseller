<?php

namespace App\Services;

use App\Events\ReportEvent;
use App\Events\NotificationEvent;
use App\Models\User;
use App\Models\WarehousesItemsExportReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class WarehouseItemsExportReportService
{
    public static function get(User $model): ?WarehousesItemsExportReport
    {
        return $model->warehousesItemsExportReports()->where('status', 2)->first();
    }

    public static function newOrFail(User $model): bool
    {
        if (static::get($model)) {
            return false;
        } else {

            $model->warehousesItemsExportReports()->create([
                'status' => 2,
                'message' => 'В процессе'
            ]);

            try {
                event(new ReportEvent($model));
            } catch (\Throwable) {

            }

            return true;
        }
    }

    public static function success(User $model, $uuid = null): bool
    {
        if ($report = static::get($model)) {
            $report->update([
                'uuid' => $uuid,
                'status' => 0,
                'message' => 'Экспорт завершен'
            ]);

            try {
                event(new NotificationEvent($model->id, 'Объект: склады', 'Экспорт завершен', 0));
            } catch (\Throwable) {

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
                'message' => 'Ошибка при экспорте'
            ]);

            try {
                event(new NotificationEvent($model->id, 'Объект: склады', 'Ошибка при экспорте', 1));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function timeout()
    {
        WarehousesItemsExportReport::where('updated_at', '<', now()->subHours(2))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (WarehousesItemsExportReport $report) {
                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время экспорта'
                    ]);

                    try {
                        event(new NotificationEvent($report->user_id, 'Объект: склады', 'Вышло время экспорта', 1));
                    } catch (\Throwable) {

                    }
                });
            });
    }

    public static function prune()
    {
        $totalDeleted = 0;

        do {
            $reports = WarehousesItemsExportReport::where('updated_at', '<', now()->subWeek())->limit(100)->get();

            foreach ($reports as $report) {

                Storage::delete('users/warehouses' . "{$report->uuid}.xlsx");

                $report->delete();
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
