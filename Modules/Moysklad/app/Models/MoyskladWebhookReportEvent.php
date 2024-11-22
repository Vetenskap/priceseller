<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MoyskladWebhookReportEvent extends MainModel
{
    protected $fillable = [
        'event',
        'message',
        'itemable_id',
        'itemable_type',
        'moysklad_webhook_report_id',
        'exception',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function itemable(): MorphTo
    {
        return $this->morphTo('itemable', 'itemable_type', 'itemable_id');
    }
}
