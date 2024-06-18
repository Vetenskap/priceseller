<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoyskladWarehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ms_uuid',
        'open',
        'moysklad_id',
    ];
}
