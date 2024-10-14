<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WbWarehouseStock extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'stock',
        'wb_warehouse_id',
        'wb_item_id',
    ];

    public function wbItem()
    {
        return $this->belongsTo(WbItem::class);
    }

}
