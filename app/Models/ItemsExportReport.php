<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemsExportReport extends MainModel
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
