<?php

namespace Modules\SamsonApi\Models;

use Illuminate\Database\Eloquent\Model;

class SamsonApiTime extends Model
{
    protected $fillable = [
        'time',
        'samson_api_id',
    ];

}
