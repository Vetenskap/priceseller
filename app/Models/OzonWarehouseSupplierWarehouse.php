<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OzonWarehouseSupplierWarehouse extends MainModel
{

    use HasFactory;

    public $fillable = [
        'ozon_warehouse_supplier_id',
        'supplier_warehouse_id',
    ];

    public function supplierWarehouse(): BelongsTo
    {
        return $this->belongsTo(SupplierWarehouse::class);
    }
}
