<?php

namespace Modules\Moysklad\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoyskladQuarantine extends MainModel
{

    protected $fillable = [
        'supplier_buy_price',
        'moysklad_id',
        'item_id',
    ];

    public function scopeFilters(Builder $query, int $price_difference_from = null, int $price_difference_to = null)
    {
        return $query->when(request('filters.price_difference_from') && request('filters.price_difference_to'), function (Builder $query) {
                $query->havingBetween('price_difference', [request('filters.price_difference_from'), request('filters.price_difference_to')]);
            })
            ->when($price_difference_from && $price_difference_to, function (Builder $query) use ($price_difference_from, $price_difference_to) {
                $query->havingBetween('price_difference', [$price_difference_from, $price_difference_to]);
            });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function moysklad()
    {
        return $this->belongsTo(Moysklad::class, 'moysklad_id', 'id');
    }

}
