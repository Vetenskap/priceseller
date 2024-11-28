<?php

namespace App\Models;

use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Moysklad\Models\MoyskladItemOrder;
use Modules\Moysklad\Models\MoyskladQuarantine;
use Modules\Moysklad\Models\MoyskladWebhookReport;
use Modules\Moysklad\Models\MoyskladWebhookReportEvent;

class Item extends MainModel
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'code',
        'supplier_id',
        'article',
        'brand',
        'user_id',
        'ms_uuid',
        'name',
        'multiplicity',
        'id',
        'unload_ozon',
        'unload_wb',
        'created_at',
        'updated_at',
        'buy_price_reserve'
    ];

    protected $casts = [
        'updated' => 'boolean',
        'unload_ozon' => 'boolean',
        'unload_wb' => 'boolean',
    ];

    const MAINATTRIBUTES = [
        [
            'name' => 'article',
            'label' => 'Артикул поставщика'
        ],
        [
            'name' => 'brand',
            'label' => 'Бренд поставщика'
        ],
        [
            'name' => 'name',
            'label' => 'Наименование'
        ],
        [
            'name' => 'multiplicity',
            'label' => 'Кратность отгрузки'
        ],
        [
            'name' => 'unload_ozon',
            'label' => 'Выгружать Озон'
        ],
        [
            'name' => 'unload_wb',
            'label' => 'Выгружать ВБ'
        ],
        [
            'name' => 'code',
            'label' => 'Код клиента'
        ],
        [
            'name' => 'buy_price_reserve',
            'label' => 'Закупочная цена резерв'
        ],
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.code'), function (Builder $query) {
            $query->where('code', 'like', '%' . request('filters.code') . '%');
        })
            ->when(request('filters.article'), function (Builder $query) {
                $query->where('article', 'like', '%' . request('filters.article') . '%');
            })
            ->when(request('filters.supplier_id'), function (Builder $query) {
                $query->where('supplier_id',  request('filters.supplier_id'));
            })
            ->when(request('filters.name'), function (Builder $query) {
                $query->where('name', 'like', '%' . request('filters.name') . '%');
            })
            ->when(!is_null(request('filters.unload_wb')), function (Builder $query) {
                $query->where('unload_wb',  request('filters.unload_wb'));
            })
            ->when(!is_null(request('filters.unload_ozon')), function (Builder $query) {
                $query->where('unload_ozon',  request('filters.unload_ozon'));
            })
            ->when(!is_null(request('filters.updated')), function (Builder $query) {
                $query->where('updated',  request('filters.updated'));
            })
            ->when(request('filters.attributes'), function (Builder $query) {
                foreach (request('filters.attributes') as $attributeId => $value) {
                    $query->whereHas('attributesValues', function ($query) use ($attributeId, $value) {
                        $query->where('item_attribute_id', $attributeId)->where('value', 'like', '%' . $value . '%');
                    });
                }
            });
    }

    public function fromPrice()
    {
        return $this->hasOne(EmailPriceItem::class);
    }

    public function warehousesStocks()
    {
        return $this->hasMany(ItemWarehouseStock::class);
    }

    public function attributesValues()
    {
        return $this->hasMany(ItemAttributeValue::class);
    }

    public function moyskladOrders()
    {
        return $this->hasMany(MoyskladItemOrder::class);
    }

    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'bundle_items')->withPivot(['multiplicity', 'id']);
    }

    public function ozonItems()
    {
        return $this->morphMany(OzonItem::class, 'ozonitemable');
    }

    public function wbItems()
    {
        return $this->morphMany(WbItem::class, 'wbitemable');
    }

    public function moyskladWebhookReportEvents(): MorphMany
    {
        return $this->morphMany(MoyskladWebhookReportEvent::class, 'itemable');
    }

    public function supplierWarehouseStocks(): HasMany
    {
        return $this->hasMany(ItemSupplierWarehouseStock::class);
    }

    public function msQuarantine(): HasOne
    {
        return $this->hasOne(MoyskladQuarantine::class, 'item_id', 'id');
    }
}
