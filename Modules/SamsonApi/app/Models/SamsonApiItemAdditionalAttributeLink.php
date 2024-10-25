<?php

namespace Modules\SamsonApi\Models;

use Illuminate\Database\Eloquent\Model;

class SamsonApiItemAdditionalAttributeLink extends MainModel
{

    protected $fillable = [
        'link',
        'link_label',
        'item_attribute_id',
        'samson_api_id',
    ];

}
