<?php

namespace Modules\Order\Models;

use App\Models\ItemWarehouseStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Database\Factories\WriteOffItemWarehouseStockFactory;

class WriteOffItemWarehouseStock extends MainModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'stock',
        'item_warehouse_stock_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function itemWarehouseStock()
    {
        return $this->belongsTo(ItemWarehouseStock::class, 'item_warehouse_stock_id', 'id');
    }

}
