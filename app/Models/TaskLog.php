<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskLog extends ReportLog
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'status',
        'payload',
        'message',
        'task_id',
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}