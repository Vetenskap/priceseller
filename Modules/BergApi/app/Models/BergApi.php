<?php

namespace Modules\BergApi\Models;

use App\Models\Supplier;
use App\Models\User;
use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BergApi extends MainModel
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

    public function itemAdditionalAttributeLinks()
    {
        return $this->hasMany(BergApiItemAdditionalAttributeLink::class);
    }

    public function times()
    {
        return $this->hasMany(BergApiTime::class);
    }

    public function warehouses()
    {
        return $this->hasMany(BergApiWarehouse::class);
    }

}
