<?php

namespace Modules\Moysklad\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MoyskladWebhook extends MainModel
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'action',
        'moysklad_webhook_uuid',
        'moysklad_id',
        'name',
        'enabled'
    ];

    public function moysklad()
    {
        return $this->belongsTo(Moysklad::class);
    }

    public function reports()
    {
        return $this->hasMany(MoyskladWebhookReport::class);
    }

}
