<?php

namespace Modules\BergApi\Models;

use Illuminate\Database\Eloquent\Model;

class BergApiTime extends MainModel
{
    protected $fillable = [
        'time',
        'berg_api_id',
    ];

}
