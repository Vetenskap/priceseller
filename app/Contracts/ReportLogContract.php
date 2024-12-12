<?php

namespace App\Contracts;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\ReportLog;

interface ReportLogContract
{
    public function new(TaskTypes $type, array $payload, Reportable $reportable): ReportLog;

    public function prune(): int;

    public function timeout(): int;

    public function failed(Report $report): bool;

    public function cancelled(Report $report): bool;

    public function finished(Report $report): bool;

    public function running(Report $report): bool;

    public function changeMessage(Report $report, string $message): bool;

    public function addLog(Report $report, string $message, ReportStatus $status = null, array $payload = null): ReportLog;
}
