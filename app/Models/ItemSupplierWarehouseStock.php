<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSupplierWarehouseStock extends Model
{
    use HasFactory;

    public $fillable = [
        'stock',
        'supplier_warehouse_id',
        'item_id',
    ];
}
