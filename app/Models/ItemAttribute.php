<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ItemAttribute extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'type',
        'user_id',
    ];
}
