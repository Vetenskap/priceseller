<?php

namespace Modules\VoshodApi\Models;

use App\Models\ItemAttribute;
use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoshodApiItemAdditionalAttributeLink extends MainModel
{
    protected $fillable = [
        'link',
        'link_label',
        'item_attribute_id',
        'voshod_api_id',
    ];

    public function itemAttribute(): BelongsTo
    {
        return $this->belongsTo(ItemAttribute::class, 'item_attribute_id', 'id');
    }
}
