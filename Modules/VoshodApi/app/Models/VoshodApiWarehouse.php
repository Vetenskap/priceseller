<?php

namespace Modules\VoshodApi\Models;

use Illuminate\Database\Eloquent\Model;

class VoshodApiWarehouse extends Model
{
    protected $fillable = [
        'name',
        'label',
        'voshod_api_id',
    ];

}
