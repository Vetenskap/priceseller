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
        'updated',
        'deleted',
        'uuid',
        'message',
        'status',
        'reportable_id',
        'reportable_type',
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
