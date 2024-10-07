<?php

namespace Modules\BergApi\Models;

use App\Models\SupplierWarehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BergApiWarehouse extends Model
{
    protected $fillable = [
        'name',
        'warehouse_id',
        'berg_api_id',
        'supplier_warehouse_id'
    ];

    public function supplierWarehouse(): BelongsTo
    {
        return $this->belongsTo(SupplierWarehouse::class, 'supplier_warehouse_id', 'id');
    }

}
