<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WbWarehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'wb_market_id',
        'warehouse_id'
    ];

    public function market()
    {
        return $this->belongsTo(WbMarket::class, 'wb_market_id', 'id');
    }

    public function suppliers()
    {
        return $this->hasMany(WbWarehouseSupplier::class);
    }
}
