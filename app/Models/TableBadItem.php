<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableBadItem extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'row',
        'attribute',
        'errors',
        'values',
        'items_import_report_id',
    ];
}
