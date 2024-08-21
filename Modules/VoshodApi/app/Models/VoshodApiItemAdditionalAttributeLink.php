<?php

namespace Modules\VoshodApi\Models;

use Illuminate\Database\Eloquent\Model;

class VoshodApiItemAdditionalAttributeLink extends Model
{
    protected $fillable = [
        'link',
        'link_label',
        'item_attribute_id',
        'voshod_api_id',
    ];
}
