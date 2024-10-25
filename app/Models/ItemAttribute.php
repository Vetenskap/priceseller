<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ItemAttribute extends MainModel
{
    use HasUuids;

    protected $fillable = [
        'name',
        'type',
        'user_id',
    ];
}
