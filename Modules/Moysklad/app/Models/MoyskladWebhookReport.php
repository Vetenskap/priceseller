<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MoyskladWebhookReport extends MainModel
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'status',
        'moysklad_webhook_id',
        'payload',
        'exception',
        'action',
        'itemable_id',
        'itemable_type',
    ];

    protected $casts = [
        'payload' => 'collection',
        'status' => 'boolean',
    ];

    public function moyskladWebhook(): BelongsTo
    {
        return $this->belongsTo(MoyskladWebhook::class, 'moysklad_webhook_id', 'id');
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo('itemable', 'itemable_type', 'itemable_id');
    }

}
