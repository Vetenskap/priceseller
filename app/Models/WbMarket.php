<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WbMarket extends MainModel
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'api_key',
        'coefficient',
        'basic_logistics',
        'price_one_liter',
        'open',
        'max_count',
        'min',
        'max',
        'volume',
        'user_id',
        'organization_id'
    ];

    public function items()
    {
        return $this->hasMany(WbItem::class);
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

    public function warehouses()
    {
        return $this->hasMany(WbWarehouse::class);
    }
}
