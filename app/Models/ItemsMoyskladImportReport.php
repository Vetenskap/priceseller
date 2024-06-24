<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsMoyskladImportReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'correct',
        'error',
        'updated',
        'message',
        'status',
        'uuid',
        'moysklad_id',
    ];

    public function badItems()
    {
        return $this->hasMany(TableMoyskladBadItem::class);
    }
}
