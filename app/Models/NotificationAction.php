<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NotificationAction extends MainModel
{
    use HasUuids;

    protected $fillable = [
        'name',
        'label',
        'description'
    ];
}
