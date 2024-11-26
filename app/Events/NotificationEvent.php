<?php

namespace App\Events;

use App\Helpers\Helpers;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public int $userId, public string $title, public string $message, public int $status, public ?string $href = null)
    {
        Notification::create([
            'user_id' => $this->userId,
            'message' => "[" . now()->setTimezone(Helpers::getUserTimeZone(User::findOrFail($this->userId))) . "] (" . $this->title . ") " . $this->message,
            'status' => $this->status,
            'href' => $this->href
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('notification.' . $this->userId),
        ];
    }

    public function broadcastAs()
    {
        return 'notify';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status,
        ];
    }
}
