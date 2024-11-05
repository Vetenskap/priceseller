<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Opcodes\LogViewer\Facades\Cache;

class OzonMarket extends MainModel
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
        'organization_id',
        'minus_stock',
        'enabled_price',
        'enabled_update_commissions_in_time',
        'update_commissions_time'
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

    public function actionReports(): MorphMany
    {
        return $this->morphMany(MarketActionReport::class, 'reportable');
    }

    public function relationships()
    {
        return $this->morphMany(MarketItemRelationship::class, 'relationshipable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suppliers(): Collection
    {
        return Cache::tags(['ozon', 'market', 'suppliers'])->rememberForever($this->id, function () {
            return Supplier::whereHas('items', function ($query) {
                $query->whereHas('ozonItems', function ($query) {
                    $query->where('ozon_items.ozon_market_id', $this->id);
                });
            })->distinct()->get();
        });
    }

    public function clearSuppliersCache(): void
    {
        Cache::tags(['ozon', 'market', 'suppliers'])->forget($this->id);
    }
}
