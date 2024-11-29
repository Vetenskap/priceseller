<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonWarehouse extends MainModel
{

    use HasFactory;

    protected $fillable = [
        'name',
        'ozon_market_id',
        'warehouse_id',
    ];

    public function market()
    {
        return $this->belongsTo(OzonMarket::class, 'ozon_market_id', 'id');
    }

    public function suppliers()
    {
        return $this->hasMany(OzonWarehouseSupplier::class);
    }

    public function userWarehouses()
    {
        return $this->hasMany(OzonWarehouseUserWarehouse::class);
    }

    public function stocks()
    {
        return $this->hasMany(OzonWarehouseStock::class);
    }
}
