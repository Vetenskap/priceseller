<?php

namespace App\Services\Bundle;

use App\Events\NotificationEvent;
use App\Models\BundlesExportReport;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Services\NotificationService;
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

            NotificationService::send($user->id, 'Комплекты', 'Экспорт завершен', 0, null, 'export');

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

            NotificationService::send($user->id, 'Комплекты', 'Ошибка при экспорте', 1, null, 'export');

            return true;
        } else {
            return false;
        }
    }
}
