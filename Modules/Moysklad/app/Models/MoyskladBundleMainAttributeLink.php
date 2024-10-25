<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;

class MoyskladBundleMainAttributeLink extends MainModel
{
    protected $fillable = [
        'attribute_name',
        'link',
        'link_name',
        'link_label',
        'type',
        'user_type',
        'moysklad_id',
    ];
}
