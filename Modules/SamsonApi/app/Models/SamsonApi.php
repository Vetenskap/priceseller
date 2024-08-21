<?php

namespace Modules\SamsonApi\Models;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SamsonApi extends Model
{
    protected $fillable = [
        'api_key',
        'supplier_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function times()
    {
        return $this->hasMany(SamsonApiTime::class);
    }

    public function itemAdditionalAttributeLinks()
    {
        return $this->hasMany(SamsonApiItemAdditionalAttributeLink::class);
    }

}
