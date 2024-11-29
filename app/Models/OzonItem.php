<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\Order;

class OzonItem extends MainModel
{

    use HasFactory;
    use HasUuids;

    const MAINATTRIBUTES = [
        ['name' => 'product_id', 'label' => 'Идентификатор товара в ОЗОН'],
        ['name' => 'offer_id', 'label' => 'Артикул клиента в ОЗОН'],
        ['name' => 'min_price_percent', 'label' => 'Минимальная цена в процентах'],
        ['name' => 'min_price', 'label' => 'Минимальная цена'],
        ['name' => 'shipping_processing', 'label' => 'Обработка отправления'],
        ['name' => 'direct_flow_trans', 'label' => 'Магистраль'],
        ['name' => 'deliv_to_customer', 'label' => 'Последняя миля'],
        ['name' => 'sales_percent', 'label' => 'Комиссия'],
        ['name' => 'price', 'label' => 'Цена'],
        ['name' => 'price_seller', 'label' => 'Цена конкурента'],
        ['name' => 'price_min', 'label' => 'Итоговая минимальная цена'],
        ['name' => 'price_max', 'label' => 'Итоговая максимальная цена'],
        ['name' => 'price_market', 'label' => 'Цена из кабинета'],
    ];

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
        'ozon_market_id',
        'ozonitemable_id',
        'ozonitemable_type',
    ];

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.market_id'), function (Builder $query) {
            $query->where('product_id', 'like', '%' . request('filters.market_id') . '%');
        })
            ->when(request('filters.market_client_code'), function (Builder $query) {
                $query->where('offer_id', 'like', '%' . request('filters.market_client_code') . '%');
            });
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

    public function warehouseStock(OzonWarehouse $warehouse): ?OzonWarehouseStock
    {
        return $warehouse->stocks()->where('ozon_item_id', $this->id)->first();
    }

    public function itemable()
    {
        return $this->morphTo('itemable', 'ozonitemable_type', 'ozonitemable_id');
    }
}
