<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;

class MoyskladRecountRetailMarkup extends MainModel
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

    protected $casts = [
        'enabled' => 'boolean',
    ];
}
