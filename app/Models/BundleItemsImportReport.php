<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BundleItemsImportReport extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'correct',
        'error',
        'deleted',
        'message',
        'status',
        'uuid',
        'user_id',
    ];
}
