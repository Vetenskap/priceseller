<?php

namespace Modules\Moysklad\Models;

use App\Models\ItemAttribute;
use Illuminate\Database\Eloquent\Model;

class MoyskladItemAdditionalAttributeLink extends Model
{
    protected $fillable = [
        'item_attribute_id',
        'link',
        'moysklad_id',
        'link_name',
        'link_label',
        'type',
        'user_type'
    ];

    public function itemAttribute()
    {
        return $this->belongsTo(ItemAttribute::class);
    }

}
