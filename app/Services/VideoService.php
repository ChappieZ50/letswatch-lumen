<?php

namespace App\Services;

use App\Rules\RecaptchaRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        return app('redis')->set($room->room_id, json_encode($room), 'ex', $roomService->createExpire());
    }
}