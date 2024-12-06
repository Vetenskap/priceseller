<?php

namespace App\Contracts;

use App\Enums\ReportStatus;
use App\Enums\TaskTypes;

interface NotificationContract
{
    public function send(int $userId, string $title, string $message, ReportStatus $status, TaskTypes $type, ?string $href): bool;
}
