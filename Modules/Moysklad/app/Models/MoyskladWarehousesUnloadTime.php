<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;

class MoyskladWarehousesUnloadTime extends MainModel
{
    protected $fillable = [
        'time',
        'moysklad_id',
    ];
}
