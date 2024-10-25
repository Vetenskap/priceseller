<?php

namespace Modules\BergApi\Models;

use Illuminate\Database\Eloquent\Model;

class BergApiItemAdditionalAttributeLink extends MainModel
{
    protected $fillable = [
        'link',
        'link_label',
        'item_attribute_id',
        'berg_api_id',
    ];

}
