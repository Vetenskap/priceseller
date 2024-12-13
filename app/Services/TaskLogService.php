<?php

namespace App\Services;

use App\Contracts\ReportLogContract;
use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\ReportLog;

class TaskLogService implements ReportLogContract
{

    public function new(Report $report, string $message, array $payload = null): ReportLog
    {
        return $report->logs()->create([
            'message' => $message,
            'status' => ReportStatus::pending,
            'payload' => $payload
        ]);
    }

    public function prune(): int
    {
        // TODO: Implement prune() method.
    }

    public function timeout(): int
    {
        // TODO: Implement timeout() method.
    }

    public function failed(ReportLog $log): bool
    {
        return $log->update([
            'status' => ReportStatus::failed
        ]);
    }

    public function cancelled(ReportLog $log): bool
    {
        return $log->update([
            'status' => ReportStatus::cancelled
        ]);
    }

    public function finished(ReportLog $log): bool
    {
        return $log->update([
            'status' => ReportStatus::finished
        ]);
    }

    public function running(ReportLog $log): bool
    {
        return $log->update([
            'status' => ReportStatus::running
        ]);
    }
}
