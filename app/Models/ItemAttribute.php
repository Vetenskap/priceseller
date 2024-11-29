<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemAttribute extends MainModel
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'type',
        'user_id',
    ];
}
