<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends MainModel
{
    public function logs(): HasMany
    {
        return $this->hasMany(ReportLog::class);
    }

    public function reportable(string $name): MorphTo
    {
        return $this->morphTo($name);
    }

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
