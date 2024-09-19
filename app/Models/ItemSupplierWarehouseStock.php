<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemSupplierWarehouseStock extends Model
{
    use HasFactory;

    public $fillable = [
        'stock',
        'supplier_warehouse_id',
        'item_id',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(SupplierWarehouse::class, 'supplier_warehouse_id', 'id');
    }
}
