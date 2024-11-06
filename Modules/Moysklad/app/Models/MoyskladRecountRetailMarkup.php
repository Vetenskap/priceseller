<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;

class MoyskladRecountRetailMarkup extends Model
{
    protected $fillable = [
        'enabled',
        'link',
        'link_name',
        'link_label',
        'link_type',
        'price_type_uuid',
        'moysklad_id',
    ];
}
