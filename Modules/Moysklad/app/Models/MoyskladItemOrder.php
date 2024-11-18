<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoyskladItemOrder extends MainModel
{
    protected $fillable = [
        'orders',
        'item_id',
        'moysklad_id',
        'new'
    ];

    protected $casts = ['new' => 'boolean'];

    public function moysklad(): BelongsTo
    {
        return $this->belongsTo(Moysklad::class);
    }
}
