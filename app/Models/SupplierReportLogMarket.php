<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReportLogMarket extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'message',
        'item_id',
        'task_log',
    ];
}
