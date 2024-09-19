<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WbWarehouseSupplier extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'wb_warehouse_id',
        'supplier_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(WbWarehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(WbWarehouseSupplierWarehouse::class);
    }
}
