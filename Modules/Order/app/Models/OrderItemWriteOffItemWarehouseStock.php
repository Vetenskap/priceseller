<?php

namespace Modules\Order\Models;

use App\Models\ItemWarehouseStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemWriteOffItemWarehouseStock extends Model
{
    protected $fillable = [
        'stock',
        'item_warehouse_stock_id',
        'order_item_id',
    ];

    public function itemWarehouseStock(): BelongsTo
    {
        return $this->belongsTo(ItemWarehouseStock::class, 'item_warehouse_stock_id', 'id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
}
