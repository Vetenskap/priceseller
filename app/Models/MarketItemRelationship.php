<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketItemRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_code',
        'code',
        'message',
        'status',
        'relationshipable_id',
        'relationshipable_type',
    ];

    public function relationshipable()
    {
        return $this->morphTo();
    }
}
