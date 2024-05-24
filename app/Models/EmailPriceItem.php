<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailPriceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'article',
        'brand',
        'price',
        'stock',
        'message',
        'status',
        'supplier_id',
        'item_id',
    ];

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.status'), function (Builder $query) {
            $query->where('status', request('filters.status'));
        })
            ->when(request('filters.article'), function (Builder $query) {
                $query->where('article', 'like', '%' . request('filters.article') . '%');
            });
    }
}
