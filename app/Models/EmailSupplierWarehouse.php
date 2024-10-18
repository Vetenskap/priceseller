<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSupplierWarehouse extends Model
{
    use HasFactory;

    public $fillable = [
        'value',
        'email_supplier_id',
        'supplier_warehouse_id',
    ];

    public function supplierWarehouse(): BelongsTo
    {
        return $this->belongsTo(SupplierWarehouse::class, 'supplier_warehouse_id', 'id');
    }

}
