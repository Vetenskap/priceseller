<?php

namespace Modules\Order\Models;

use App\Models\OzonMarket;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderOzonWebhook extends Model
{
    use HasUuids;

    protected $fillable = [
        'ozon_market_id'
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(OzonMarket::class, 'ozon_market_id', 'id');
    }
}
