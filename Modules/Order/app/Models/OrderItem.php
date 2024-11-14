<?php

namespace Modules\Order\Models;

use App\Models\Item;
use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends MainModel
{
    protected $fillable = [
        'item_id',
        'order_id',
        'multiplicity',
        'count'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function writeOffStocks(): HasMany
    {
        return $this->hasMany(OrderItemWriteOffItemWarehouseStock::class, 'order_item_id', 'id');
    }

}
