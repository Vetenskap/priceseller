<?php

namespace App\Services;

use App\Events\ItemsImportReportEvent;
use App\Events\NotificationEvent;
use App\Models\ItemsImportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\WbMarket;
use App\Services\Item\ItemService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ItemsImportReportService
{
    public static function get(OzonMarket|WbMarket|User $model): ?ItemsImportReport
    {
        return $model->itemsImportReports()->where('status', 2)->first();
    }

    public static function new(OzonMarket|WbMarket|User $model, string $uuid): ?ItemsImportReport
    {
        if (static::get($model)) {
            return null;
        } else {
            return $model->itemsImportReports()->create([
                'status' => 2,
                'message' => 'В процессе',
                'uuid' => $uuid,
            ]);
        }
    }

    public static function flush(OzonMarket|WbMarket|User $model, int $correct, int $error): bool
    {
        if ($report = static::get($model)) {

            $report->update([
                'correct' => $correct,
                'error' => $error,
            ]);

            try {
                event(new ItemsImportReportEvent($model));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function addBadItem(OzonMarket|WbMarket|User $model, int $row, string $attribute, array $errors, array $values): bool
    {
        if ($report = static::get($model)) {

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

    public static function success(OzonMarket|WbMarket|User $model, int $correct, int $error, ?string $uuid = null): bool
    {
        if ($report = static::get($model)) {
            $report->update([
                'uuid' => $uuid,
                'correct' => $correct,
                'error' => $error,
                'status' => 0,
                'message' => 'Импорт завершен'
            ]);

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, 'Объект: ' . $model->name, 'Импорт завершен', 0));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function error(OzonMarket|WbMarket|User $model): bool
    {
        if ($report = static::get($model)) {
            $report->update([
                'status' => 1,
                'message' => 'Ошибка при импорте'
            ]);

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, 'Объект: ' . $model->name, 'Ошибка при импорте', 1));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function timeout()
    {
        ItemsImportReport::where('updated_at', '<', now()->subminutes(30))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (ItemsImportReport $report) {
                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время импорта'
                    ]);

                    try {
                        event(new NotificationEvent($report->reportable->user_id ?? $report->reportable->id, 'Объект: ' . $report->reportable->name, 'Вышло время импорта', 1));
                    } catch (\Throwable) {

                    }

                });
            });
    }

    public static function prune()
    {
        $totalDeleted = 0;

        do {
            $reports = ItemsImportReport::where('updated_at', '<', now()->subWeek())->limit(100)->get();

            foreach ($reports as $report) {

                if ($report->reportable instanceof OzonMarket) {
                    Storage::delete(OzonMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof WbMarket) {
                    Storage::delete(WbMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof User) {
                    Storage::delete(ItemService::PATH . "{$report->uuid}.xlsx");
                }
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
