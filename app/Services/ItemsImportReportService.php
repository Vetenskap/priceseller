<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Models\ItemsImportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\Item\ItemService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ItemsImportReportService
{
    public static function destroy(ItemsImportReport $report, OzonMarket|WbMarket|User|Warehouse $model): void
    {
        if ($report->status === 2) abort(403);

        $status = Storage::disk('public')->delete(ItemsExportReportService::getPath($model) . "{$report->uuid}.xlsx");
        if ($status) $report->delete();
    }

    public static function get(OzonMarket|WbMarket|User|Warehouse $model): ?ItemsImportReport
    {
        return $model->itemsImportReports()->where('status', 2)->first();
    }

    public static function new(OzonMarket|WbMarket|User|Warehouse $model, string $uuid): bool
    {
        if (static::get($model)) {
            return false;
        } else {

            $model->itemsImportReports()->create([
                'status' => 2,
                'message' => 'В процессе',
                'uuid' => $uuid,
            ]);

            return true;
        }
    }

    public static function flush(OzonMarket|WbMarket|User|Warehouse $model, int $correct, int $error, int $updated, int $deleted = 0): bool
    {
        if ($report = static::get($model)) {

            $report->update([
                'correct' => $correct,
                'error' => $error,
                'updated' => $updated,
                'deleted' => $deleted,
            ]);

            return true;
        } else {
            return false;
        }
    }

    public static function addBadItem(OzonMarket|WbMarket|User|Warehouse $model, int $row, string $attribute, array $errors, array $values): bool
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

    public static function success(OzonMarket|WbMarket|User|Warehouse $model, int $correct, int $error, int $updated, int $deleted = 0, ?string $uuid = null): bool
    {
        if ($report = static::get($model)) {

            $report->correct = $correct;
            $report->error = $error;
            $report->updated = $updated;
            $report->deleted = $deleted;
            $report->status = 0;
            $report->message = 'Импорт завершен';

            if ($uuid) {
                $report->uuid = $uuid;
            }

            $report->save();

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, $model->name, 'Импорт завершен', 0));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }

    public static function error(OzonMarket|WbMarket|User|Warehouse $model): bool
    {
        if ($report = static::get($model)) {
            $report->update([
                'status' => 1,
                'message' => 'Ошибка при импорте'
            ]);

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, $model->name, 'Ошибка при импорте', 1));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }

    public static function timeout(): void
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
                        event(new NotificationEvent($report->reportable->user_id ?? $report->reportable->id, $report->reportable->name, 'Вышло время импорта', 1));
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
            $reports = ItemsImportReport::where('updated_at', '<', now()->subWeek())->limit(100)->get();

            foreach ($reports as $report) {

                if ($report->reportable instanceof OzonMarket) {
                    Storage::delete(OzonMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof WbMarket) {
                    Storage::delete(WbMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof User) {
                    Storage::delete(ItemService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof Warehouse) {
                    Storage::delete(WarehouseService::PATH . "{$report->uuid}.xlsx");
                }

                $report->delete();
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
