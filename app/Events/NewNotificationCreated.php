<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NewNotificationCreated implements ShouldBroadcast
{
    use SerializesModels;

    public $notification;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Notification $notification, $userId)
    {
        $this->notification = $notification;
        $this->userId = $userId;
    }

    /**
     * The name of the channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }

    /**
     * The name of the event (optional — default is the class name).
     */
    public function broadcastAs()
    {
        return 'NewNotificationCreated';
    }

    /**
     * Data to be sent to the frontend (optional — Laravel will use public properties otherwise).
     */
    public function broadcastWith()
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'title' => $this->notification->title,
                'message' => $this->notification->message,
                'created_at' => $this->notification->created_at->toDateTimeString(),
            ],
        ];
    }
}
