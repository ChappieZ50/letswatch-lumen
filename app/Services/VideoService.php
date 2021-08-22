<?php

namespace App\Services;

class VideoService
{
    public function setSeek($seek, $room)
    {

        $roomService = new RoomService();

        $room->player = [
            'url'  => $room->player->url,
            'type' => $room->player->type,
            'seek' => $seek
        ];
        // Override room to new room data
        return app('redis')->set($room->room_id, json_encode($room), 'ex', $roomService->createExpire());
    }
}
