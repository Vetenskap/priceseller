<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OzonWarehouseUserWarehouse extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'ozon_warehouse_id',
        'warehouse_id',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function ozonWarehouse(): BelongsTo
    {
        return $this->belongsTo(OzonWarehouse::class, 'ozon_warehouse_id', 'id');
    }
}
