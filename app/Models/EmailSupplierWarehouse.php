<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSupplierWarehouse extends Model
{
    use HasFactory;

    public $fillable = [
        'value',
        'email_supplier_id',
        'supplier_warehouse_id',
    ];
}
