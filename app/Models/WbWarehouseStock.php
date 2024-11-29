<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WbWarehouseStock extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'stock',
        'wb_warehouse_id',
        'wb_item_id',
    ];

    public function wbItem(): BelongsTo
    {
        return $this->belongsTo(WbItem::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WbWarehouse::class, 'wb_warehouse_id', 'id');
    }

}
