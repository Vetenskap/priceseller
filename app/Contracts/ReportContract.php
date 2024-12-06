<?php

namespace App\Contracts;

use App\Enums\TaskTypes;
use App\Models\Contracts\Reportable;
use App\Models\Report;

interface ReportContract
{
    public function new(TaskTypes $type, array $payload, Reportable $reportable): Report;

    public function prune(): int;

    public function timeout(): int;

    public function failed(Report $report): bool;

    public function cancelled(Report $report): bool;

    public function finished(Report $report): bool;

    public function running(Report $report): bool;

    public function changeMessage(Report $report, string $message): bool;
}
