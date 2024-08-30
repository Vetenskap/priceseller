<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleItemsImportReport extends Model
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
