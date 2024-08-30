<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BundleItem extends Model
{
    protected $fillable = [
        'bundle_id',
        'item_id',
        'multiplicity'
    ];
}
