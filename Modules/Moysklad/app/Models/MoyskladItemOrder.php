<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoyskladItemOrder extends Model
{
    protected $fillable = [
        'orders',
        'item_id',
        'moysklad_id',
    ];

    public function moysklad(): BelongsTo
    {
        return $this->belongsTo(Moysklad::class);
    }
}
