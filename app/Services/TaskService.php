<?php

namespace App\Services;

use App\Contracts\NotificationContract;
use App\Contracts\ReportContract;
use App\Enums\ReportStatus;
use App\Enums\TaskTypes;
use App\Models\Contracts\Reportable;
use App\Models\Report;

class TaskService implements ReportContract
{
    public function __construct(public NotificationContract $notification)
    {

    }

    public function new(TaskTypes $type, array $payload, Reportable $reportable): Report
    {
        $report = $reportable->reports()->create([
            'type' => $type,
            'payload' => $payload,
            'status' => ReportStatus::pending
        ]);

        $this->notification->send($reportable->getUserId(), $reportable->getTitle(), 'Создан', ReportStatus::pending, $type, null);

        return $report;
    }

    public function prune(): int
    {
        // TODO: Implement prune() method.
    }

    public function timeout(): int
    {
        // TODO: Implement timeout() method.
    }

    public function failed(Report $report): bool
    {
        $status = $report->update([
            'status' => ReportStatus::failed
        ]);

        if ($status) {
            $this->notification->send($report->taskable->getUserId(), $report->taskable->getTitle(), 'Ошибка', ReportStatus::failed, $report->type, null);
        }

        return $status;
    }

    public function cancelled(Report $report): bool
    {
        $status = $report->update([
            'status' => ReportStatus::cancelled
        ]);

        if ($status) {
            $this->notification->send($report->taskable->getUserId(), $report->taskable->getTitle(), 'Отменен', ReportStatus::cancelled, $report->type, null);
        }

        return $status;
    }

    public function finished(Report $report): bool
    {
        $status = $report->update([
            'status' => ReportStatus::finished
        ]);

        if ($status) {
            $this->notification->send($report->taskable->getUserId(), $report->taskable->getTitle(), 'Завершен', ReportStatus::finished, $report->type, null);
        }

        return $status;
    }

    public function running(Report $report): bool
    {
        $status = $report->update([
            'status' => ReportStatus::running
        ]);

        if ($status) {
            $this->notification->send($report->taskable->getUserId(), $report->taskable->getTitle(), 'В процессе', ReportStatus::running, $report->type, null);
        }

        return $status;
    }

    public function changeMessage(Report $report, string $message): bool
    {
        return $report->update([
            'message' => $message
        ]);
    }
}
