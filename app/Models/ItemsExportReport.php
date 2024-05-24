<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsExportReport extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
