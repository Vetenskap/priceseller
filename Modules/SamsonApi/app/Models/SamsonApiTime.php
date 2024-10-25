<?php

namespace Modules\SamsonApi\Models;

use App\Models\MainModel;

class SamsonApiTime extends MainModel
{
    protected $fillable = [
        'time',
        'samson_api_id',
    ];

}
