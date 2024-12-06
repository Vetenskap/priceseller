<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function taskable(): MorphTo
    {
        return parent::reportable('taskable');
    }

    protected $casts = [
        'payload' => 'array'
    ];
}
