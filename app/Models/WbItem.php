<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        'id',
        'nm_id',
        'vendor_code',
        'sku',
        'sales_percent',
        'min_price',
        'retail_markup_percent',
        'package',
        'volume',
        'price_market',
        'item_id',
        'wb_market_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
