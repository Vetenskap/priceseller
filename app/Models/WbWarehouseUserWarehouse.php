<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WbWarehouseUserWarehouse extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'wb_warehouse_id',
        'warehouse_id',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function wbWarehouse(): BelongsTo
    {
        return $this->belongsTo(WbWarehouse::class, 'wb_warehouse_id', 'id');
    }
}
