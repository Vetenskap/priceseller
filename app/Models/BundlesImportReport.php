<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundlesImportReport extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'correct',
        'error',
        'updated',
        'deleted',
        'message',
        'status',
        'uuid',
        'user_id',
    ];
}
