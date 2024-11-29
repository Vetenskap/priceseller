<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ItemsImportReport extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'correct',
        'error',
        'message',
        'status',
        'uuid',
        'reportable_id',
        'reportable_type',
        'updated',
        'deleted',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }

    public function badItems()
    {
        return $this->hasMany(TableBadItem::class);
    }
}
