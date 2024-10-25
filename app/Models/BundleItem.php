<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BundleItem extends MainModel
{
    protected $fillable = [
        'bundle_id',
        'item_id',
        'multiplicity'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
