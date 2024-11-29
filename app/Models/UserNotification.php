<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserNotification extends MainModel
{

    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'enabled_telegram',
        'telegram_chat_id',
        'user_id',
        'enabled_site',
    ];

    protected $casts = [
        'enabled_telegram' => 'boolean',
        'enabled_site' => 'boolean',
    ];

    public function actions(): HasMany
    {
        return $this->hasMany(UserNotificationAction::class, 'user_notification_id', 'id');
    }
}
