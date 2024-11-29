<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBaseSetting extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'enabled_use_buy_price_reserve',
        'user_id',
    ];

    protected $casts = [
        'enabled_use_buy_price_reserve' => 'boolean',
    ];
}
