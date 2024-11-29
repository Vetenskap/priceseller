<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModule extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'enabled',
        'module_id',
        'user_id',
    ];

    protected $casts = ['enabled' => 'boolean'];
}
