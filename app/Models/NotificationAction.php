<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationAction extends MainModel
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'label',
        'description',
    ];
}
