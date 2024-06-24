<?php

namespace App\Services;

use App\Models\ItemsMoyskladImportReport;
use App\Models\Moysklad;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ItemsMoyskladImportReportService
{
    public static function get(Moysklad $moysklad): ?ItemsMoyskladImportReport
    {
        return $moysklad->itemsImportReports()->where('status', 2)->first();
    }

    public static function new(Moysklad $moysklad, string $uuid = null): ?ItemsMoyskladImportReport
    {
        if (static::get($moysklad)) {
            return null;
        } else {
            $report = $moysklad->itemsImportReports()->create([
                'status' => 2,
                'message' => 'В процессе',
            ]);
            if ($uuid) {
                $report->uuid = $uuid;
                $report->save();
            }
            return $report;
        }
    }

    public static function flush(Moysklad $moysklad, int $correct, int $error, int $updated): bool
    {
        if ($report = static::get($moysklad)) {

            $report->update([
                'correct' => $correct,
                'error' => $error,
                'updated' => $updated,
            ]);

            try {
//                event(new ItemsImportReportEvent($model));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function addBadItem(Moysklad $moysklad, int $row, string $attribute, array $errors, array $values): bool
    {
        if ($report = static::get($moysklad)) {

            $report->badItems()->create([
                'row' => $row,
                'attribute' => $attribute,
                'errors' => json_encode($errors),
                'values' => json_encode($values),
            ]);

            return true;
        } else {
            return false;
        }
    }

    public static function success(Moysklad $moysklad, int $correct, int $error, int $updated): bool
    {
        if ($report = static::get($moysklad)) {

            $report->correct = $correct;
            $report->error = $error;
            $report->updated = $updated;
            $report->status = 0;
            $report->message = 'Импорт завершен';

            $report->save();

            try {
//                event(new NotificationEvent($model->user_id ?? $model->id, 'Объект: ' . $model->name, 'Импорт завершен', 0));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function error(Moysklad $moysklad): bool
    {
        if ($report = static::get($moysklad)) {
            $report->update([
                'status' => 1,
                'message' => 'Ошибка при импорте'
            ]);

            try {
//                event(new NotificationEvent($model->user_id ?? $model->id, 'Объект: ' . $model->name, 'Ошибка при импорте', 1));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function timeout()
    {
        ItemsMoyskladImportReport::where('updated_at', '<', now()->subminutes(30))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (ItemsMoyskladImportReport $report) {
                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время импорта'
                    ]);

                    try {
//                        event(new NotificationEvent($report->reportable->user_id ?? $report->reportable->id, 'Объект: ' . $report->reportable->name, 'Вышло время импорта', 1));
                    } catch (\Throwable) {

                    }

                });
            });
    }

    public static function prune()
    {
        $totalDeleted = 0;

        do {
            $reports = ItemsMoyskladImportReport::where('updated_at', '<', now()->subWeek())->limit(100)->get();

            foreach ($reports as $report) {

                Storage::delete(MoyskladService::PATH . "{$report->uuid}.xlsx");
                $report->delete();
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
