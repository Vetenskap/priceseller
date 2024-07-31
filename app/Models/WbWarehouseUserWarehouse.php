<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WbWarehouseUserWarehouse extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'wb_warehouse_id',
        'warehouse_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
