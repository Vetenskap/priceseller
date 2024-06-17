<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OzonMarket extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'client_id',
        'api_key',
        'min_price_percent',
        'max_price_percent',
        'seller_price_percent',
        'open',
        'max_count',
        'min',
        'max',
        'seller_price',
        'acquiring',
        'last_mile',
        'max_mile',
        'user_id',
    ];

    public function items()
    {
        return $this->hasMany(OzonItem::class);
    }

    public function warehouses()
    {
        return $this->hasMany(OzonWarehouse::class);
    }

    public function itemsImportReports()
    {
        return $this->morphMany(ItemsImportReport::class, 'reportable');
    }

    public function itemsExportReports()
    {
        return $this->morphMany(ItemsExportReport::class, 'reportable');
    }

    public function relationships()
    {
        return $this->morphMany(MarketItemRelationship::class, 'relationshipable');
    }
}
