<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousesItemsImportReport extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'correct',
        'error',
        'message',
        'status',
        'uuid',
        'user_id',
    ];
}
