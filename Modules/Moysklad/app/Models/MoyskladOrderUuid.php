<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;

class MoyskladOrderUuid extends MainModel
{
    protected $fillable = [
        'uuid',
        'moysklad_id',
    ];
}
