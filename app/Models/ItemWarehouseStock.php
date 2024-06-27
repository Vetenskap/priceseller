<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemWarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock',
        'item_id',
        'warehouse_id',
    ];
}
