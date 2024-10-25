<?php

namespace Modules\SamsonApi\Models;

use Illuminate\Database\Eloquent\Model;

class SamsonApiTime extends MainModel
{
    protected $fillable = [
        'time',
        'samson_api_id',
    ];

}
