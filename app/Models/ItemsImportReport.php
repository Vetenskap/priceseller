<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsImportReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'correct',
        'error',
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
