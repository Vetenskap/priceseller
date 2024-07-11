<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonWarehouseSupplier extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'ozon_warehouse_id',
        'supplier_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(OzonWarehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
