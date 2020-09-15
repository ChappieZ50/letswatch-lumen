<?php

namespace App\Http\Controllers;

use App\Events\Chat\OnMessageSend;
use App\Services\ChatService;
use App\Services\RoomService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Predis\Client;

class ChatController extends Controller
{
    protected $redis;

    public function __construct()
    {
        $this->redis = app('redis')->connection('chat');
    }

    public function store(RoomService $roomService, ChatService $service, Request $request)
    {
        $this->validate($request, $service->rules(), [
            'room_id.uuid' => 'Room not validated',
            'user_id.uuid' => 'User not validated',
        ]);

        $roomId = $request->get('room_id');
        $userId = $request->get('user_id');
        $message = $request->get('message');

        $room = $roomService->userAndRoomExists($roomId, $userId);

        if ($room !== false) {
            $user = $roomService->getUserInRoom($room, $userId);
            $chat = $service->exists($roomId);

            $payload = [
                'user'       => $user,
                'room_id'    => $roomId,
                'message'    => $message,
                'created_at' => Carbon::now(),
            ];

            $save = $service->save($payload, $roomId, $chat);
            if ($save) {
                event(new OnMessageSend($roomId, $payload));
                return response('', Response::HTTP_NO_CONTENT);
            }
            return response('', Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        return response()->json(['message' => 'Room not found'], Response::HTTP_NOT_FOUND);
    }

    /* TODO */
    public function get(Client $client)
    {
        //$this->redis->flushDb();
        dd(json_decode($this->redis->get('71d5a4ed-e3e1-4b01-9a64-5d28b1d1b2a4')));
        dd($this->redis->keys('*'));
    }
}
