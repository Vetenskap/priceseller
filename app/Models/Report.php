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
        return $this->status == ReportStatus::cancelled->name;
    }

    public function isFinished(): bool
    {
        return $this->status === ReportStatus::finished->name;
    }

    public function isRunning(): bool
    {
        return $this->status === ReportStatus::running->name;
    }

    public function isFailed(): bool
    {
        return $this->status === ReportStatus::failed->name;
    }

    public function isPending(): bool
    {
        return $this->status === ReportStatus::pending->name;
    }
}
