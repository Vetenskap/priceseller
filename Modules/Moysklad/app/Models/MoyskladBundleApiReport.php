<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoyskladBundleApiReport extends MainModel
{
    use HasUuids;

    protected $fillable = [
        'status',
        'message',
        'updated',
        'created',
        'errors',
        'moysklad_id',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MoyskladBundleApiReportItem::class);
    }

}
