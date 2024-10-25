<?php

namespace Modules\SamsonApi\Models;

use App\Models\Supplier;
use App\Models\SupplierWarehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SamsonApi extends MainModel
{
    protected $fillable = [
        'api_key',
        'supplier_id',
        'user_id',
        'supplier_warehouse_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function times(): HasMany
    {
        return $this->hasMany(SamsonApiTime::class);
    }

    public function itemAdditionalAttributeLinks(): HasMany
    {
        return $this->hasMany(SamsonApiItemAdditionalAttributeLink::class);
    }

    public function supplierWarehouse(): BelongsTo
    {
        return $this->belongsTo(SupplierWarehouse::class, 'supplier_warehouse_id', 'id');
    }

}
