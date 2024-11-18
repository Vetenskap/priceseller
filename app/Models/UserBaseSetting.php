<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBaseSetting extends MainModel
{
    protected $fillable = [
        'enabled_use_buy_price_reserve',
        'user_id',
    ];

    protected $casts = [
        'enabled_use_buy_price_reserve' => 'boolean',
    ];
}
