<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Models\WriteOffItemWarehouseStock;

class ItemWarehouseStock extends MainModel
{
    use HasFactory;

    protected $fillable = [
        'stock',
        'item_id',
        'warehouse_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function writeOffStock()
    {
        return $this->hasOne(WriteOffItemWarehouseStock::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
