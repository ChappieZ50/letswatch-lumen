<?php

namespace App\Http\Controllers;

use App\Events\OnClose;
use App\Events\OnConnect;
use App\Events\OnExit;
use App\Events\OnPlayer;
use App\Services\RoomService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    public function get($roomId, $userId, RoomService $service)
    {
        $room = $service->userInRoom($roomId, $userId);
        // If user not in room
        if ($room === false)
            return response()->json(['message' => 'You cant access this room'], Response::HTTP_UNAUTHORIZED);

        return response()->json($room);
    }

    public function store(RoomService $service, Request $request)
    {
        // Validating request
        $this->validate($request, $service->rules());

        // Saving to database
        $saved = $service->save($request);

        // If saved is failed return 500 error else return saved data
        return !$saved ? response()->json([
            'message' => 'Failed to create room'
        ], Response::HTTP_INTERNAL_SERVER_ERROR) : response()->json($saved);

    }

    public function join(Request $request, RoomService $service)
    {
        // Validating data
        $this->validate($request, $service->mergeRule([
            'room_id' => 'required|uuid',
        ]), [
            'room_id.uuid' => 'Room not found'
        ]);

        // Add user to room users
        $join = $service->join($request);

        if ($join) {
            // Fire event and notify all browsers "new user added"
            event(new OnConnect($join, $request->get('room_id')));
            return response()->json($join);
        }

        return response()->json(['message' => 'Failed to join room'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function newPlayer(Request $request, RoomService $service)
    {
        // Validating data
        $this->validate($request, [
            'url'     => 'required',
            'type'    => 'required|string',
            'seek'    => 'required|numeric',
            'user_id' => 'required|uuid',
            'room_id' => 'required|uuid',
        ], [
            'room_id.uuid' => 'Room not found'
        ]);
        $roomId = $request->get('room_id');
        $userId = $request->get('user_id');

        // If user and room exists
        if ($room = $service->userAndRoomExists($roomId, $userId)) {
            // Changing data
            $room->player = [
                'url'  => $request->get('url'),
                'type' => $request->get('type'),
                'seek' => $request->get('seek'),
            ];
            // Saving new player data to room
            if (app('redis')->set($roomId, json_encode($room))) {
                // Firing OnPlayer event for all browsers
                event(new OnPlayer($roomId,$room));
                return response()->json($room);
            }
            return response()->json(['message' => 'Failed to change player'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json(['message' => 'Room or user not found'], Response::HTTP_NOT_FOUND);
    }

    public function destroy($roomId, $userId, RoomService $service)
    {
        // If room exists
        if ($room = $service->exists($roomId)) {
            // If user is owner
            if ($room->owner_id === $userId) {
                // Destroy room
                if (app('redis')->del($roomId)) {
                    event(new OnClose($roomId));
                    return response()->json(['message' => 'Room deleted']);
                }
                return response()->json(['message' => 'Failed to delete room'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // If user is not owner. Just delete user from database
            $data = $service->deleteUserFromRoom($roomId, $userId);
            if ($data) {
                event(new OnExit($data, $roomId));
                return response()->json($data);
            }
            return response()->json(['message' => 'Failed to exit room'], Response::HTTP_INTERNAL_SERVER_ERROR);

        } else {
            return response()->json(['message' => 'Room not exists'], Response::HTTP_NOT_FOUND);
        }
    }

    public function all()
    {
        return app('redis')->keys('*');
    }

    public function flush()
    {
        app('redis')->flushAll();
    }
}
