<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'supplier_report_id',
    ];
}
