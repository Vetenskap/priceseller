<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Model;

class MoyskladWebhookReport extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'status',
        'moysklad_webhook_id',
    ];

}
