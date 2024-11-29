<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MarketActionReport extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'action',
        'message',
        'status',
        'reportable_id',
        'reportable_type',
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
