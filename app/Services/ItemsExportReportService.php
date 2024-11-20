<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Models\ItemsExportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\Item\ItemService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelIdea\Helper\App\Models\_IH_ItemsExportReport_QB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemsExportReportService
{
    const MODEL_TO_PATH = [
        'App\Models\User' => [
            'path' => ItemService::PATH,
            'filename' => ItemService::FILENAME,
        ],
        'App\Models\OzonMarket' => OzonMarketService::PATH,
        'App\Models\WbMarket' => WbMarketService::PATH,
    ];

    public static function getPath(OzonMarket|WbMarket|User|Warehouse $model): string
    {
        return is_array(static::MODEL_TO_PATH[get_class($model)])
            ? static::MODEL_TO_PATH[get_class($model)]['path']
            : static::MODEL_TO_PATH[get_class($model)];
    }

    public static function getFilename(OzonMarket|WbMarket|User|Warehouse $model)
    {
        return is_array(static::MODEL_TO_PATH[get_class($model)])
            ? static::MODEL_TO_PATH[get_class($model)]['filename']
            : $model->name;
    }

    public static function download(ItemsExportReport $report, OzonMarket|WbMarket|User|Warehouse $model): BinaryFileResponse
    {
        if ($report->status === 2) abort(403);

        return response()->download(
            file: Storage::disk('public')->path(static::getPath($model) . "{$report->uuid}.xlsx"),
            name: static::getFilename($model) . "_{$report->updated_at}.xlsx"
        );
    }

    public static function destroy(ItemsExportReport $report, OzonMarket|WbMarket|User|Warehouse $model): void
    {
        if ($report->status === 2) abort(403);

        $status = Storage::disk('public')->delete(static::getPath($model) . "{$report->uuid}.xlsx");
        if ($status) $report->delete();
    }

    public static function get(OzonMarket|WbMarket|User|Warehouse $model): Model|MorphMany|MorphToMany|_IH_ItemsExportReport_QB|null
    {
        return $model->itemsExportReports()->where('status', 2)->first();
    }

    public static function new(OzonMarket|WbMarket|User|Warehouse $model): bool
    {
        if (static::get($model)) {
            return false;
        } else {

            $model->itemsExportReports()->create([
                'status' => 2,
                'message' => 'В процессе'
            ]);

            return true;
        }
    }

    public static function success(OzonMarket|WbMarket|User|Warehouse $model, $uuid = null): bool
    {
        if ($report = static::get($model)) {
            $report->update([
                'uuid' => $uuid,
                'status' => 0,
                'message' => 'Экспорт завершен'
            ]);

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, $model->name, 'Экспорт завершен', 0));
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
                'message' => 'Ошибка при экспорте'
            ]);

            try {
                event(new NotificationEvent($model->user_id ?? $model->id, $model->name, 'Ошибка при экспорте', 1));
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
        ItemsExportReport::where('updated_at', '<', now()->subHours(4))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (ItemsExportReport $report) {
                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время экспорта'
                    ]);

                    try {
                        event(new NotificationEvent($report->reportable->user_id ?? $report->reportable->id, $report->reportable->name, 'Вышло время экспорта', 1));
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
            $reports = ItemsExportReport::where('updated_at', '<', now()->subWeek())->limit(100)->get();

            foreach ($reports as $report) {

                if ($report->reportable instanceof OzonMarket) {
                    Storage::delete(OzonMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof WbMarket) {
                    Storage::delete(WbMarketService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof User) {
                    Storage::delete(ItemService::PATH . "{$report->uuid}.xlsx");
                } else if ($report->reportable instanceof  Warehouse) {
                    Storage::delete(WarehouseService::PATH . "{$report->uuid}.xlsx");
                }

                $report->delete();
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
