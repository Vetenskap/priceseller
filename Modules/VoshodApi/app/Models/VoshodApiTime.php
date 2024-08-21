<?php

namespace Modules\VoshodApi\Models;

use Illuminate\Database\Eloquent\Model;

class VoshodApiTime extends Model
{

    protected $fillable = [
        'time',
        'voshod_api_id',
    ];

}
