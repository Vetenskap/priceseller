<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;

class MoyskladItemOrder extends Model
{
    protected $fillable = [
        'orders',
        'item_id',
        'moysklad_id',
    ];
}
