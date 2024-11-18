<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MoyskladItemApiReportItem extends MainModel
{
    use HasUuids;

    protected $fillable = [
        'status',
        'message',
        'data',
        'exception',
        'moysklad_item_api_report_id',
    ];

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.data'), function (Builder $query) {
            $query->where('data', 'like', '%' . request('filters.data') . '%');
        });
    }

}
