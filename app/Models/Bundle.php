<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Moysklad\Models\MoyskladWebhookReport;

class Bundle extends MainModel
{
    use HasUuids;

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'bundle_items')->withPivot(['multiplicity', 'updated_at', 'created_at']);
    }

    protected $fillable = [
        'code',
        'name',
        'user_id',
        'ms_uuid',
        'id'
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

    public function ozonItems(): MorphMany
    {
        return $this->morphMany(OzonItem::class, 'ozonitemable');
    }

    public function wbItems(): MorphMany
    {
        return $this->morphMany(WbItem::class, 'wbitemable');
    }

    public function moyskladWebhookReports(): MorphMany
    {
        return $this->morphMany(MoyskladWebhookReport::class, 'itemable');
    }
}
