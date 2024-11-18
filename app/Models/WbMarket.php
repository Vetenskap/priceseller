<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\Assembly\Models\AssemblyWbSupply;
use Opcodes\LogViewer\Facades\Cache;

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
        'organization_id',
        'minus_stock',
        'enabled_update_commissions_in_time',
        'update_commissions_time',
        'tariff'
    ];

    protected $casts = [
        'enabled_update_commissions_in_time' => 'boolean',
        'open' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(WbItem::class);
    }

    public function supplies(): HasMany
    {
        return $this->hasMany(AssemblyWbSupply::class, 'wb_market_id', 'id');
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

    public function warehouses()
    {
        return $this->hasMany(WbWarehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suppliers(): Collection
    {
        return Cache::tags(['wb', 'market', 'suppliers'])->rememberForever($this->id, function () {
            return Supplier::whereHas('items', function ($query) {
                $query->whereHas('wbItems', function ($query) {
                    $query->where('wb_items.wb_market_id', $this->id);
                });
            })->distinct()->get();
        });
    }

    public function clearSuppliersCache(): void
    {
        Cache::tags(['wb', 'market', 'suppliers'])->forget($this->id);
    }
}
