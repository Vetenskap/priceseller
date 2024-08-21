<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBaseSetting extends Model
{
    protected $fillable = [
        'enabled_use_buy_price_reserve',
        'user_id',
    ];
}
