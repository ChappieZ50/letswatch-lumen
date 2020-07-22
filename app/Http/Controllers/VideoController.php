<?php

namespace App\Http\Controllers;

use App\Events\OnPlaying;
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

        // If room exists
        if ($room = $roomService->exists($roomId)) {
            // Request has seek key
            if ($request->has('seek')) {
                $seek = $request->get('seek');
                // Set new seek to room
                $service->setSeek($seek, $room);
            } else {
                // Else use old seek
                $seek = $room->player->seek;
            }

            // Fire OnPlaying event and notify all browsers
            event(new OnPlaying($playing, $seek, $roomId, $userId));
            return response()->json([], 201);
        }

        return response()->json(['status' => false], 404);
    }
}
