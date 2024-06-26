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
        'id'
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
            });
    }

    public function fromPrice()
    {
        return $this->hasOne(EmailPriceItem::class);
    }
}
