<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\Order;

class OzonItem extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'product_id',
        'offer_id',
        'min_price_percent',
        'min_price',
        'shipping_processing',
        'direct_flow_trans',
        'deliv_to_customer',
        'sales_percent',
        'price',
        'price_seller',
        'price_min',
        'price_max',
        'price_market',
        'count',
        'item_id',
        'ozon_market_id',
        'id'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function orders()
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function market()
    {
        return $this->belongsTo(OzonMarket::class, 'ozon_market_id', 'id');
    }

    public function stocks()
    {
        return $this->hasMany(OzonWarehouseStock::class);
    }

    public function warehouseStock(OzonWarehouse $warehouse): OzonWarehouseStock
    {
        return $warehouse->stocks()->where('ozon_item_id', $this->id)->first();
    }
}
