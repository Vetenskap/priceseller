<?php

namespace App\Services\Bundle;

use App\Events\NotificationEvent;
use App\Events\ReportEvent;
use App\Models\BundleItemsImportReport;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BundleItemsImportReportService
{
    public static function destroy(BundleItemsImportReport $report): void
    {
        if ($report->status === 2) abort(403);

        $status = Storage::disk('public')->delete(BundleItemsService::PATH . "{$report->uuid}.xlsx");
        if ($status) $report->delete();
    }

    public static function get(User $user): ?BundleItemsImportReport
    {
        return $user->bundleItemsImportReports()->where('status', 2)->first();
    }

    public static function new(User $user, string $uuid): bool
    {
        if (static::get($user)) {
            return false;
        } else {

            $user->bundleItemsImportReports()->create([
                'status' => 2,
                'message' => 'В процессе',
                'uuid' => $uuid,
            ]);

            try {
                event(new ReportEvent($user->id));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        }
    }

    public static function flush(User $user, int $correct, int $error, int $updated, int $deleted = 0): bool
    {
        if ($report = static::get($user)) {

            $report->update([
                'correct' => $correct,
                'error' => $error,
                'updated' => $updated,
                'deleted' => $deleted,
            ]);

            try {
                event(new ReportEvent($user->id));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }

//    public static function addBadItem(User $user, int $row, string $attribute, array $errors, array $values): bool
//    {
//        if ($report = static::get($user)) {
//
//            $report->badItems()->create([
//                'row' => $row,
//                'attribute' => $attribute,
//                'errors' => json_encode($errors),
//                'values' => json_encode($values),
//            ]);
//
//            return true;
//        } else {
//            return false;
//        }
//    }

    public static function success(User $user, int $correct, int $error, int $updated, int $deleted = 0, ?string $uuid = null): bool
    {
        if ($report = static::get($user)) {

            $report->correct = $correct;
            $report->error = $error;
            $report->deleted = $deleted;
            $report->status = 0;
            $report->message = 'Импорт завершен';

            if ($uuid) {
                $report->uuid = $uuid;
            }

            $report->save();


            try {
                event(new NotificationEvent($user->id, 'Объект: комплекты', 'Импорт завершен', 0));
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
                'message' => 'Ошибка при импорте'
            ]);

            try {
                event(new NotificationEvent($user->id, 'Объект: комплекты', 'Ошибка при импорте', 1));
            } catch (\Throwable $e) {
                report($e);
            }

            return true;
        } else {
            return false;
        }
    }
}