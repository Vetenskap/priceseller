<?php

namespace Modules\Assembly\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Assembly\Database\Factories\AssemblyProductSettingsFactory;

class AssemblyProductSettings extends Model
{
    protected $fillable = [
        'field',
        'label',
        'type',
        'additional',
        'market',
        'color',
        'size_level',
        'index',
        'user_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
