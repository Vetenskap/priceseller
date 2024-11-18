<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MoyskladBundleApiReportItem extends MainModel
{
    use HasUuids;

    protected $fillable = [
        'status',
        'message',
        'data',
        'exception',
        'moysklad_bundle_api_report_id',
    ];

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.data'), function (Builder $query) {
            $query->where('data', 'like', '%' . request('filters.data') . '%');
        });
    }

}
