<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Moysklad\Models\MoyskladWebhookReport;
use Modules\Moysklad\Models\MoyskladWebhookReportEvent;

class Bundle extends MainModel
{
    use HasFactory, HasUuids;

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'bundle_items')->withPivot(['multiplicity', 'updated_at', 'created_at']);
    }

    protected $fillable = [
        'ms_uuid',
        'code',
        'name',
        'user_id',
    ];

    const MAINATTRIBUTES = [
        [
            'name' => 'name',
            'label' => 'Наименование'
        ],
        [
            'name' => 'code',
            'label' => 'Код клиента'
        ],
    ];

    protected static function booted()
    {
        static::deleting(function (Bundle $bundle) {
            foreach ($bundle->ozonItems as $ozonItem) {
                $ozonItem->delete();
            }
            foreach ($bundle->wbItems as $wbItem) {
                $wbItem->delete();
            }
        });
    }

    public function scopeFilters(Builder $query)
    {
        return $query->when(request('filters.code'), function (Builder $query) {
            $query->where('code', 'like', '%' . request('filters.code') . '%');
        })
            ->when(request('filters.name'), function (Builder $query) {
                $query->where('name', 'like', '%' . request('filters.name') . '%');
            })
            ->when(request('filters.items.code'), function (Builder $query) {
                $query->whereHas('items', function (Builder $query) {
                    $query->where('code', 'like', '%' . request('filters.items.code') . '%');
                });
            });
    }

    public function ozonItems(): MorphMany
    {
        return $this->morphMany(OzonItem::class, 'ozonitemable');
    }

    public function wbItems(): MorphMany
    {
        return $this->morphMany(WbItem::class, 'wbitemable');
    }

    public function moyskladWebhookReportEvents(): MorphMany
    {
        return $this->morphMany(MoyskladWebhookReportEvent::class, 'itemable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
