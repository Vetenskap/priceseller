<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Events\Supplier\SupplierReportChangeMessage;
use App\Models\Supplier;
use App\Models\SupplierReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class SupplierReportService
{
    public static function get(Supplier $supplier): ?SupplierReport
    {
        return $supplier->reports()->where('status', 2)->first();
    }

    public static function new(Supplier $supplier, string $path): bool
    {
        if (static::get($supplier)) {
            return false;
        } else {
            $supplier->reports()->create([
                'status' => 2,
                'message' => 'Начало выгрузки',
                'path' => $path
            ]);

            static::addLog($supplier, 'Начало выгрузки');

            return true;
        }
    }

    public static function addLog(Supplier $supplier, string $message): bool
    {
        if ($report = static::get($supplier)) {

            $report->logs()->create([
                'message' => $message
            ]);

            return true;
        }
        return false;
    }

    public static function changeMessage(Supplier $supplier, string $message): bool
    {
        if ($report = static::get($supplier)) {
            $report->message = $message;
            $report->save();

            try {
                event(new SupplierReportChangeMessage($supplier));
            } catch (\Throwable) {

            }

            sleep(1);

            static::addLog($supplier, $message);

            return true;
        }
        return false;
    }

    public static function success(Supplier $supplier): bool
    {
        if ($report = static::get($supplier)) {

            sleep(1);

            static::addLog($supplier, 'Поставщик успешно выгружен');

            $report->message = 'Поставщик успешно выгружен';
            $report->status = 0;
            $report->save();

            return true;
        }

        return false;
    }

    public static function error(Supplier $supplier): bool
    {
        if ($report = static::get($supplier)) {

            sleep(1);

            static::addLog($supplier, 'Ошибка в выгрузке');

            $report->message = 'Ошибка в выгрузке';
            $report->status = 1;
            $report->save();

            return true;
        }

        return false;
    }

    public static function timeout()
    {
        SupplierReport::where('updated_at', '<', now()->subHours(2))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (SupplierReport $report) {

                    static::addLog($report->supplier, 'Вышло время выгрузки');

                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время выгрузки'
                    ]);

                    try {
                        event(new NotificationEvent($report->supplier->user_id, 'Объект: ' . $report->supplier->name, 'Вышло время выгрузки', 1));
                    } catch (\Throwable) {

                    }
                });
            });
    }

    public static function prune()
    {
        $totalDeleted = 0;

        do {
            $reports = SupplierReport::where('updated_at', '<', now()->subDay())->limit(100)->get();

            foreach ($reports as $report) {
                Storage::delete(SupplierService::PATH . "{$report->uuid}.xlsx");
                $report->delete();
            }

            $totalDeleted += $reports->count();

        } while ($reports->count() > 0);

        return $totalDeleted;
    }
}
