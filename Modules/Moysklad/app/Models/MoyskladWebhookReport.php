<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;

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
    ];

    protected $casts = [
        'payload' => 'collection',
        'status' => 'boolean',
    ];

    public function moyskladWebhook()
    {
        return $this->belongsTo(MoyskladWebhook::class, 'moysklad_webhook_id', 'id');
    }

}
