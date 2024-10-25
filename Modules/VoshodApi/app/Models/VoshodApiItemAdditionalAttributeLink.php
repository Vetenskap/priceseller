<?php

namespace Modules\VoshodApi\Models;

use App\Models\MainModel;

class VoshodApiItemAdditionalAttributeLink extends MainModel
{
    protected $fillable = [
        'link',
        'link_label',
        'item_attribute_id',
        'voshod_api_id',
    ];
}
