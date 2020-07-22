<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OnPlayer implements ShouldBroadcast
{

    use SerializesModels;

    public $roomId;
    public $room;

    public function __construct($roomId,$room)
    {
        $this->roomId = $roomId;
        $this->room = $room;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomId);
    }

    public function broadcastWith()
    {
        return [
            'status' => true,
            'room'   => $this->room
        ];
    }
}
