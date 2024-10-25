<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;

class MoyskladItemMainAttributeLink extends MainModel
{
    protected $fillable = [
        'attribute_name',
        'link',
        'moysklad_id',
        'link_name',
        'link_label',
        'type',
        'user_type',
        'invert'
    ];

}
