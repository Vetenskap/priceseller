<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportLog extends MainModel
{
    public function isCancelled(): bool
    {
        return $this->status === ReportStatus::cancelled;
    }

    public function isFinished(): bool
    {
        return $this->status === ReportStatus::finished;
    }

    public function isRunning(): bool
    {
        return $this->status === ReportStatus::running;
    }

    public function isFailed(): bool
    {
        return $this->status === ReportStatus::failed;
    }

    public function isPending(): bool
    {
        return $this->status === ReportStatus::pending;
    }
}
