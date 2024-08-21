<?php

namespace Modules\BergApi\Models;

use Illuminate\Database\Eloquent\Model;

class BergApiTime extends Model
{
    protected $fillable = [
        'time',
        'berg_api_id',
    ];

}
