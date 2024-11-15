<?php

namespace Modules\Assembly\Models;

use App\Models\MainModel;

class AssemblyProductSettings extends MainModel
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
        'in_table',
    ];

    protected $hidden = ['created_at', 'updated_at'];

}
