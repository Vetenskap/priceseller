<?php

namespace Modules\Assembly\Models;

use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssemblyWbSupply extends Model
{
    protected $fillable = [
        'id_supply',
        'name',
        'closed_at',
        'scan_dt',
        'cargo_type',
        'wb_market_id',
        'count_orders',
        'done',
        'updated_at'
    ];

    protected $casts = [
        'done' => 'boolean'
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(WbMarket::class, 'wb_market_id', 'id');
    }
}
