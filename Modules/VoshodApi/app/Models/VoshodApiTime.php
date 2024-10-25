<?php

namespace Modules\VoshodApi\Models;

use App\Models\MainModel;

class VoshodApiTime extends MainModel
{

    protected $fillable = [
        'time',
        'voshod_api_id',
    ];

}
