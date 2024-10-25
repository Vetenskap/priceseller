<?php

namespace Modules\BergApi\Models;

use App\Models\MainModel;

class BergApiTime extends MainModel
{
    protected $fillable = [
        'time',
        'berg_api_id',
    ];

}
