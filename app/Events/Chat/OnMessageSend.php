<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OnMessageSend implements ShouldBroadcast
{

    use SerializesModels;

    public $roomId;
    public $message;

    public function __construct($roomId, $message)
    {
        $this->roomId = $roomId;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomId);
    }

    public function broadcastWith()
    {
        return [
            'status'  => true,
            'message' => $this->message
        ];
    }
}
