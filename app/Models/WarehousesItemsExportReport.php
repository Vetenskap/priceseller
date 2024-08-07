<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousesItemsExportReport extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'message',
        'status',
        'user_id',
    ];
}
