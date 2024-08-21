<?php

namespace Modules\BergApi\Models;

use Illuminate\Database\Eloquent\Model;

class BergApiWarehouse extends Model
{
    protected $fillable = [
        'name',
        'warehouse_id',
        'berg_api_id',
    ];

}
