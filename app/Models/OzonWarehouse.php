<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OzonWarehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'name',
        'ozon_market_id',
    ];

    public function market()
    {
        return $this->belongsTo(OzonMarket::class, 'ozon_market_id', 'id');
    }

    public function suppliers()
    {
        return $this->hasMany(OzonWarehouseSupplier::class);
    }
}
