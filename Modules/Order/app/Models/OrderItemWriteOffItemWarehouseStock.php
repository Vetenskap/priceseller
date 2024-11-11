<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemWriteOffItemWarehouseStock extends Model
{
    protected $fillable = [
        'stock',
        'item_warehouse_stock_id',
        'order_item_id',
    ];
}
