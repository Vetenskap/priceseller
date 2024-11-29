<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonWarehouseUserWarehouse extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'ozon_warehouse_id',
        'warehouse_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
