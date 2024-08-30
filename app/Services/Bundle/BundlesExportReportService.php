<?php

namespace App\Services\Bundle;

use App\Events\NotificationEvent;
use App\Events\ReportEvent;
use App\Models\BundlesExportReport;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BundlesExportReportService
{
    public static function download(BundlesExportReport $report): BinaryFileResponse
    {
        if ($report->status === 2) abort(403);

        return response()->download(
            file: Storage::disk('public')->path(BundleService::PATH . "{$report->uuid}.xlsx"),
            name: BundleService::FILENAME . "_{$report->updated_at}.xlsx"
        );
    }

    public static function destroy(BundlesExportReport $report): void
    {
        if ($report->status === 2) abort(403);

        $status = Storage::disk('public')->delete(BundleService::PATH . "{$report->uuid}.xlsx");
        if ($status) $report->delete();
    }

    public static function get(User $user): ?BundlesExportReport
    {
        return $user->bundlesExportReports()->where('status', 2)->first();
    }

    public static function new(User $user): bool
    {
        if (static::get($user)) {
            return false;
        } else {

            $user->bundlesExportReports()->create([
                'status' => 2,
                'message' => 'В процессе'
            ]);

            try {
                event(new ReportEvent($user->id));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        }
    }

    public static function success(User $user, $uuid = null): bool
    {
        if ($report = static::get($user)) {
            $report->update([
                'uuid' => $uuid,
                'status' => 0,
                'message' => 'Экспорт завершен'
            ]);

            try {
                event(new NotificationEvent($user->id, 'Объект: комплекты', 'Экспорт завершен', 0));
            } catch (\Throwable $e) {
                report($e);
            }

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
                'message' => 'Ошибка при экспорте'
            ]);

            try {
                event(new NotificationEvent($user->id, 'Объект: комплекты', 'Ошибка при экспорте', 1));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }
}
