<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MarketActionReport extends Model
{
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
