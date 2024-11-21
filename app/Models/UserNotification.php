<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserNotification extends MainModel
{
    use HasUuids;

    protected $fillable = [
        'enabled_telegram',
        'telegram_chat_id',
        'user_id',
    ];

    protected $casts = [
        'enabled_telegram' => 'boolean',
    ];

    public function actions(): HasMany
    {
        return $this->hasMany(UserNotificationAction::class, 'user_notification_id', 'id');
    }
}
