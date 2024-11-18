<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends MainModel
{
    protected $fillable = [
        'label',
        'name',
    ];
}
