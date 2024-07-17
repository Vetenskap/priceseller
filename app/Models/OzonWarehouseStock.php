<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonWarehouseStock extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'stock',
        'ozon_warehouse_id',
        'ozon_item_id',
    ];

    public function ozonItem()
    {
        return $this->belongsTo(OzonItem::class);
    }
}
