<?php

namespace Modules\VoshodApi\Models;

use App\Models\SupplierWarehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoshodApiWarehouse extends MainModel
{
    protected $fillable = [
        'name',
        'label',
        'voshod_api_id',
        'supplier_warehouse_id'
    ];

    public function supplierWarehouse(): BelongsTo
    {
        return $this->belongsTo(SupplierWarehouse::class, 'supplier_warehouse_id', 'id');
    }

}
