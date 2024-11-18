<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModule extends MainModel
{
    protected $fillable = [
        'enabled',
        'module_id',
        'user_id',
    ];

    protected $casts = ['enabled' => 'boolean'];
}
