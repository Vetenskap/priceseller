<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Report
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'type',
        'status',
        'payload',
        'taskable_id',
        'taskable_type',
        'message'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(TaskLog::class);
    }

    public function taskable(): MorphTo
    {
        return parent::reportable('taskable');
    }
}
