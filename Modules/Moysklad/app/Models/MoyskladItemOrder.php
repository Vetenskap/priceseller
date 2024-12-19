<?php

namespace Modules\Moysklad\Models;

use App\Models\Item;
use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoyskladItemOrder extends MainModel
{
    use HasFactory;

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

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
