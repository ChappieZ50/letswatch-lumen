<?php

namespace App\Http\Controllers;

use App\Events\OnPlaying;
use App\Events\OnSeek;
use App\Services\RoomService;
use App\Services\VideoService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function onPlaying(Request $request, RoomService $roomService, VideoService $service)
    {
        $roomId = $request->get('room_id');
        $playing = $request->get('playing');
        $userId = $request->get('user_id');

        if ($room = $roomService->exists($roomId)) {
            if ($request->has('seek')) {
                $seek = $request->get('seek');
                $service->setSeek($seek, $room);
            } else {
                $seek = $room->player->seek;
            }

            event(new OnPlaying($playing, $seek, $roomId, $userId));
            return response()->json([], 201);
        }

        return response()->json(['status' => false], 404);
    }
}
