<?php

namespace App\Contracts;

use App\Models\Report;
use App\Models\ReportLog;

interface ReportLogContract
{
    public function new(Report $report, string $message, array $payload = null): ReportLog;

    public function prune(): int;

    public function timeout(): int;

    public function failed(ReportLog $log): bool;

    public function cancelled(ReportLog $log): bool;

    public function finished(ReportLog $log): bool;

    public function running(ReportLog $log): bool;
}
