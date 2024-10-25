<?php

namespace Modules\Moysklad\Models;

use App\Models\ItemAttribute;
use App\Models\MainModel;

class MoyskladItemAdditionalAttributeLink extends MainModel
{
    protected $fillable = [
        'item_attribute_id',
        'link',
        'moysklad_id',
        'link_name',
        'link_label',
        'type',
        'user_type',
        'invert'
    ];

    public function itemAttribute()
    {
        return $this->belongsTo(ItemAttribute::class);
    }

}
