<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationAction extends MainModel
{

    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'enabled',
        'notification_action_id',
        'user_notification_id',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(NotificationAction::class, 'notification_action_id', 'id');
    }
}
