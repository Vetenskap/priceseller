<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableMoyskladBadItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'row',
        'attribute',
        'errors',
        'values',
        'items_moysklad_import_report_id',
    ];
}
