<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;

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
