<?php

namespace Modules\VoshodApi\Models;

use Illuminate\Database\Eloquent\Model;

class VoshodApiTime extends MainModel
{

    protected $fillable = [
        'time',
        'voshod_api_id',
    ];

}
