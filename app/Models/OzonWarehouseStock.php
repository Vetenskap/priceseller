<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OzonWarehouseStock extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'stock',
        'ozon_warehouse_id',
        'ozon_item_id',
    ];

    public function ozonItem(): BelongsTo
    {
        return $this->belongsTo(OzonItem::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(OzonWarehouse::class, 'ozon_warehouse_id', 'id');
    }
}
