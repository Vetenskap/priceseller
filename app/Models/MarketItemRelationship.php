<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketItemRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_code',
        'code',
        'message',
        'status',
        'relationshipable_id',
        'relationshipable_type',
    ];

    public function relationshipable()
    {
        return $this->morphTo();
    }

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.external_code'), function (Builder $query) {
            $query->where('external_code', 'like', '%' . request('filters.external_code') . '%');
        })
            ->when(request('filters.code'), function (Builder $query) {
                $query->where('code', 'like', '%' . request('filters.code') . '%');
            })
            ->when(!is_null(request('filters.status')), function (Builder $query) {
                $query->where('status', request('filters.status'));
            });
    }
}
