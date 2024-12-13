<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupplierReportLogMarket extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'message',
        'logable_id',
        'logable_type',
        'task_log',
    ];

    public function logable(): MorphTo
    {
        return $this->morphTo();
    }
}
