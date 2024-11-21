<?php

namespace App\Services\Bundle;

use App\Events\NotificationEvent;
use App\Models\BundleItemsExportReport;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BundleItemsExportReportService
{
    public static function download(BundleItemsExportReport $report): BinaryFileResponse
    {
        if ($report->status === 2) abort(403);

        return response()->download(
            file: Storage::disk('public')->path(BundleItemsService::PATH . "{$report->uuid}.xlsx"),
            name: BundleItemsService::FILENAME . "_{$report->updated_at}.xlsx"
        );
    }

    public static function destroy(BundleItemsExportReport $report): void
    {
        if ($report->status === 2) abort(403);

        $status = Storage::disk('public')->delete(BundleItemsService::PATH . "{$report->uuid}.xlsx");
        if ($status) $report->delete();
    }

    public static function get(User $user): ?BundleItemsExportReport
    {
        return $user->bundleItemsExportReports()->where('status', 2)->first();
    }

    public static function new(User $user): bool
    {
        if (static::get($user)) {
            return false;
        } else {

            $user->bundleItemsExportReports()->create([
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

            try {
                event(new NotificationEvent($user->id, 'Комплекты', 'Экспорт завершен', 0));

                if (
                    $user->userNotification &&
                    $user->userNotification->enabled_telegram &&
                    $user->userNotification->actions()->where('enabled', true)->whereHas('action', fn ($q) => $q->where('name', 'export'))->exists()
                ) {
                    $user->notify(new UserNotification('Комплекты', 'Экспорт завершен'));
                }
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
                event(new NotificationEvent($user->id, 'Комплекты', 'Ошибка при экспорте', 1));

                if (
                    $user->userNotification &&
                    $user->userNotification->enabled_telegram &&
                    $user->userNotification->actions()->where('enabled', true)->whereHas('action', fn ($q) => $q->where('name', 'export'))->exists()
                ) {
                    $user->notify(new UserNotification('Комплекты', 'Ошибка при экспорте'));
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }
}
