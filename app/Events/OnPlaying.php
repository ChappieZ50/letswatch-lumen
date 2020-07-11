<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OnPlaying implements ShouldBroadcast
{

    use SerializesModels;

    public $playing;
    public $seek;
    public $roomId;
    public $userId;

    public function __construct($playing, $seek, $roomId, $userId)
    {
        $this->playing = $playing;
        $this->seek = $seek;

        $this->roomId = $roomId;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel('video-actions.' . $this->roomId);
    }

    public function broadcastWith()
    {
        return [
            'status'  => true,
            'playing' => $this->playing,
            'seek'    => $this->seek,
            'user_id' => $this->userId,
        ];
    }
}
