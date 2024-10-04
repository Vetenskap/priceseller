<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'wb_market_id',
        'id',
        'wbitemable_id',
        'wbitemable_type'
    ];

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.market_id'), function (Builder $query) {
            $query->where('nm_id', 'like', '%' . request('filters.market_id') . '%');
        })
            ->when(request('filters.market_client_code'), function (Builder $query) {
                $query->where('vendor_code', 'like', '%' . request('filters.market_client_code') . '%');
            });
    }

    public function orders()
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function market()
    {
        return $this->belongsTo(WbMarket::class, 'wb_market_id', 'id');
    }

    public function warehouseStock(WbWarehouse $warehouse): ?WbWarehouseStock
    {
        return $warehouse->stocks()->where('wb_item_id', $this->id)->first();
    }

    public function stocks()
    {
        return $this->hasMany(WbWarehouseStock::class);
    }

    public function wbitemable()
    {
        return $this->morphTo('wbitemable', 'wbitemable_type', 'wbitemable_id');
    }
}
