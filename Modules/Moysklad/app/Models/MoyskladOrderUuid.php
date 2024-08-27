<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;

class MoyskladOrderUuid extends Model
{
    protected $fillable = [
        'uuid',
        'moysklad_id',
    ];
}
