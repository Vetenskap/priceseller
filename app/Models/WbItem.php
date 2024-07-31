<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\Order;

class WbItem extends MainModel
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'nm_id',
        'vendor_code',
        'sku',
        'sales_percent',
        'min_price',
        'retail_markup_percent',
        'package',
        'volume',
        'price_market',
        'item_id',
        'wb_market_id',
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
        return $this->belongsTo(WbMarket::class, 'wb_market_id', 'id');
    }

    public function warehouseStock(WbWarehouse $warehouse)
    {
        return $warehouse->stocks()->where('wb_item_id', $this->id)->first();
    }

    public function stocks()
    {
        return $this->hasMany(WbWarehouseStock::class);
    }
}
