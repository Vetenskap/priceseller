<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierReport extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'status',
        'message',
        'supplier_id',
    ];
}
