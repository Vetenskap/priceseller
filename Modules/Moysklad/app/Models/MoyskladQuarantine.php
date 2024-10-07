<?php

namespace Modules\Moysklad\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoyskladQuarantine extends Model
{

    protected $fillable = [
        'supplier_buy_price',
        'moysklad_id',
        'item_id',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function moysklad()
    {
        return $this->belongsTo(Moysklad::class, 'moysklad_id', 'id');
    }

}
