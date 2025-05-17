<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RealTimeNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;
    public $userRole;
    public $type;
    public $date;
    public $userId;
    public $appointmentId;

    public function __construct($message, $userRole, $type, $date, $userId, $appointmentId)
    {
        $this->message = $message;
        $this->userRole = $userRole; 
        $this->type = $type;
        $this->date = $date;
        $this->userId = $userId;
        $this->appointmentId = $appointmentId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    // public function broadcastOn(): array
    // {
    //     // return [
    //     //     new PrivateChannel('channel-name'),
    //     // ];
    //     return [
    //          new Channel('notifications.'.$this->userId),
    //     ];
    // }

    public function broadcastOn(): array
    {
        return [
            new Channel('appointement.'.$this->appointmentId),
            new Channel('notifications.'.$this->userRole.'.'.$this->userId),
        ];
    }

    public function broadcastAs()
    {
        return 'notification-event';
    }
}
