<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Models\ItemsExportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\WbMarket;
use App\Services\Item\ItemService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ItemsExportReportService
{
    public static function get(OzonMarket|WbMarket|User $model)
    {
        return $model->itemsExportReports()->where('status', 2)->first();
    }

    public static function newOrFirst(OzonMarket|WbMarket|User $model)
    {
        if ($report = static::get($model)) {
            return $report;
        } else {
            $model->itemsExportReports()->create([
                'status' => 2,
                'message' => 'В процессе'
            ]);
        }
    }

    public static function success(OzonMarket|WbMarket|User $model, $uuid = null): bool
    {
        if ($report = static::get($model)) {
            $report->update([
                'uuid' => $uuid,
                'status' => 0,
                'message' => 'Экспорт завершен'
            ]);

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, 'Объект: ' . $model->name, 'Экспорт завершен', 0));
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
                'message' => 'Ошибка при экспорте'
            ]);

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, 'Объект: ' . $model->name, 'Ошибка при экспорте', 1));
            } catch (\Throwable) {

            }

            return true;
        } else {
            return false;
        }
    }

    public static function timeout()
    {
        ItemsExportReport::where('updated_at', '<', now()->subHours(2))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (ItemsExportReport $report) {
                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время экспорта'
                    ]);

                    try {
                        event(new NotificationEvent($report->reportable->user_id ?? $report->reportable->id, 'Объект: ' . $report->reportable->name, 'Вышло время экспорта', 1));
                    } catch (\Throwable) {

                    }
                });
            });
    }

    public static function prune()
    {
        $totalDeleted = 0;

        do {
            $reports = ItemsExportReport::where('updated_at', '<', now()->subWeek())->limit(100)->get();

            foreach ($reports as $report) {

                if ($report->reportable instanceof OzonMarket) {
                    Storage::delete(OzonMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof WbMarket) {
                    Storage::delete(WbMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof User) {
                    Storage::delete(ItemService::PATH . "{$report->uuid}.xlsx");
                }

                $report->delete();
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
