<?php

namespace Modules\BergApi\Models;

use App\Models\MainModel;

class BergApiItemAdditionalAttributeLink extends MainModel
{
    protected $fillable = [
        'link',
        'link_label',
        'item_attribute_id',
        'berg_api_id',
    ];

}
