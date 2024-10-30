<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;

class MoyskladOrderUuid extends MainModel
{
    protected $fillable = [
        'uuid',
        'moysklad_id',
    ];
}
