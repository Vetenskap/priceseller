<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'code',
        'supplier_id',
        'article',
        'brand',
        'user_id',
        'ms_uuid',
        'name',
        'multiplicity',
        'id',
        'unload_ozon',
        'unload_wb'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.code'), function (Builder $query) {
            $query->where('code', 'like', '%' . request('filters.code') . '%');
        })
            ->when(request('filters.article'), function (Builder $query) {
                $query->where('article', 'like', '%' . request('filters.article') . '%');
            })
            ->when(request('filters.supplier_id'), function (Builder $query) {
                $query->where('supplier_id',  request('filters.supplier_id'));
            })
            ->when(request('filters.name'), function (Builder $query) {
                $query->where('name', 'like', '%' . request('filters.name') . '%');
            })
            ->when(!is_null(request('filters.unload_wb')), function (Builder $query) {
                $query->where('unload_wb',  request('filters.unload_wb'));
            })
            ->when(!is_null(request('filters.unload_ozon')), function (Builder $query) {
                $query->where('unload_ozon',  request('filters.unload_ozon'));
            });
    }

    public function fromPrice()
    {
        return $this->hasOne(EmailPriceItem::class);
    }

    public function warehousesStocks()
    {
        return $this->hasMany(ItemWarehouseStock::class);
    }
}
