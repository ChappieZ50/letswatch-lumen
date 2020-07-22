<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OnConnect implements ShouldBroadcast
{

    use SerializesModels;

    public $room;
    public $roomId;

    public function __construct($room,$roomId)
    {
        $this->room = $room;
        $this->roomId = $roomId;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomId);
    }

    public function broadcastWith()
    {
        return [
            'status' => true,
            'room'   => json_decode($this->room)
        ];
    }
}
