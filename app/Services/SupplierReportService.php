<?php

namespace App\Services;

use App\Events\NotificationEvent;
use App\Models\Supplier;
use App\Models\SupplierReport;
use App\Notifications\UserNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SupplierReportService
{
    public static function get(Supplier $supplier): ?SupplierReport
    {
        return $supplier->reports()->where('status', 2)->first();
    }

    public static function new(Supplier $supplier, string $path = null, string $message = null): bool
    {
        if (static::get($supplier)) {
            return false;
        } else {
            $supplier->reports()->create([
                'status' => 2,
                'message' => 'Начало выгрузки' . ($message ? ': ' . $message : ''),
                'path' => $path
            ]);

            static::addLog($supplier, 'Начало выгрузки' . ($message ? ': ' . $message : ''));

            return true;
        }
    }

    public static function addLog(Supplier $supplier, string $message, string $level = 'info'): bool
    {
        if ($report = static::get($supplier)) {

            $report->logs()->create([
                'message' => $message,
                'level' => $level
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

            static::addLog($supplier, $message);

            return true;
        }
        return false;
    }

    public static function success(Supplier $supplier, string $message = null): bool
    {
        if ($report = static::get($supplier)) {

            static::addLog($supplier, 'Поставщик успешно выгружен' . ($message ? ': ' . $message : ''));

            $report->message = 'Поставщик успешно выгружен' . ($message ? ': ' . $message : '');
            $report->status = 0;
            $report->save();

            try {
                event(new NotificationEvent($supplier->user_id, $supplier->name, 'Поставщик успешно выгружен' . ($message ? ': ' . $message : ''), 0, route('supplier.report.edit', ['supplier' => $report->supplier, 'report' => $report])));

                $user = $supplier->user;

                if (
                    $user->userNotification &&
                    $user->userNotification->enabled_telegram &&
                    $user->userNotification->actions()->where('enabled', true)->whereHas('action', fn ($q) => $q->where('name', 'supplier'))->exists()
                ) {
                    $user->notify(new UserNotification($supplier->name, 'Поставщик успешно выгружен' . ($message ? ': ' . $message : '')));
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        }

        return false;
    }

    public static function error(Supplier $supplier, string $message = null): bool
    {
        if ($report = static::get($supplier)) {

            static::addLog($supplier, 'Ошибка в выгрузке' . ($message ? ': ' . $message : ''));

            $report->message = 'Ошибка в выгрузке' . ($message ? ': ' . $message : '');
            $report->status = 1;
            $report->save();

            try {
                event(new NotificationEvent($supplier->user_id, $supplier->name, 'Ошибка в выгрузке' . ($message ? ': ' . $message : ''), 1, route('supplier.report.edit', ['supplier' => $report->supplier, 'report' => $report])));

                $user = $supplier->user;

                if (
                    $user->userNotification &&
                    $user->userNotification->enabled_telegram &&
                    $user->userNotification->actions()->where('enabled', true)->whereHas('action', fn ($q) => $q->where('name', 'supplier'))->exists()
                ) {
                    $user->notify(new UserNotification($supplier->name, 'Ошибка в выгрузке' . ($message ? ': ' . $message : '')));
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        }

        return false;
    }

    public static function timeout(): void
    {
        SupplierReport::where('updated_at', '<', now()->subHours(4))
            ->where('status', 2)
            ->chunk(100, function (Collection $reports) {
                $reports->each(function (SupplierReport $report) {

                    static::addLog($report->supplier, 'Вышло время выгрузки');

                    $report->update([
                        'status' => 1,
                        'message' => 'Вышло время выгрузки'
                    ]);

                    try {
                        event(new NotificationEvent($report->supplier->user_id, $report->supplier->name, 'Вышло время выгрузки', 1, route('supplier.report.edit', ['supplier' => $report->supplier, 'report' => $report])));

                        $user = $report->supplier->user;

                        if (
                            $user->userNotification &&
                            $user->userNotification->enabled_telegram &&
                            $user->userNotification->actions()->where('enabled', true)->whereHas('action', fn ($q) => $q->where('name', 'supplier'))->exists()
                        ) {
                            $user->notify(new UserNotification($report->supplier->name, 'Вышло время выгрузки'));
                        }
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
