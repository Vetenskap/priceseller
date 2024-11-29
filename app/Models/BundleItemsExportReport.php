<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BundleItemsExportReport extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'uuid',
        'message',
        'status',
        'user_id',
    ];
}
