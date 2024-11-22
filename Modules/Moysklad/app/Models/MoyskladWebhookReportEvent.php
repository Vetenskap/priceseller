<?php

namespace Modules\Moysklad\Models;

use App\Models\MainModel;

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
}
