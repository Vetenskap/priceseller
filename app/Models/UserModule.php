<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModule extends Model
{
    protected $fillable = [
        'enabled',
        'module_id',
        'user_id',
    ];
}
