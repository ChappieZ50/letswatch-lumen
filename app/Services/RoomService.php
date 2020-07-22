<?php

namespace App\Services;

use App\Rules\RecaptchaRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomService
{

    public function createHash()
    {
        // Generating uuid string
        return Str::uuid();
    }

    public function createExpire()
    {
        // Generating 12 hours
        return 3600 * 12; // 12 Hours
    }

    public function rules()
    {
        // Request validate rules
        return [
            'username'  => 'required|max:15|min:3|string',
            'recaptcha' => ['required', 'string', new RecaptchaRule()],
            'user_id'   => 'required|uuid',
            'gender'    => 'required|in:male,female'
        ];
    }

    public function mergeRule($rule)
    {
        // We can add new rules
        return array_merge($rule, $this->rules());
    }

    public function save(Request $request)
    {
        // Creating uuid string
        $roomId = $this->createHash();

        // If room not exists
        if (!$this->exists($roomId)) {
            // Creating expiring time
            $expire = $this->createExpire();

            // Createing room data
            return app('redis')->set($roomId, json_encode([
                'room_id'  => $roomId,
                'owner_id' => $request->get('user_id'),
                'users'    => [
                    [
                        'username' => $request->get('username'),
                        'user_id'  => $request->get('user_id'),
                        'gender'   => $request->get('gender'),
                        'owner'    => true,
                    ]
                ],
                'player'   => [
                    'url'  => 'https://youtu.be/WkUP-oztu5I',
                    'type' => 'youtube',
                    'seek' => 0
                ]
            ]), 'ex', $expire) ? app('redis')->get($roomId) : false;
        }
        return false;
    }

    public function join(Request $request)
    {
        $roomId = $request->get('room_id');
        $userId = $request->get('user_id');

        // If room exists
        if ($room = $this->exists($roomId)) {
            // If user not in this room
            if ($this->userInRoom($roomId, $userId) === false) {
                // Adding user to room
                $room->users[] = [
                    'username' => $request->get('username'),
                    'user_id'  => $userId,
                    'gender'   => $request->get('gender'),
                    'owner'    => false,
                ];
                // Saving new room data
                return app('redis')->set($roomId, json_encode($room)) ? app('redis')->get($roomId) : false;
            }
            // If user already in this room then get room
            return app('redis')->get($roomId);
        }

        return false;
    }

    public function deleteUserFromRoom($roomId, $userId)
    {
        // Getting room and json decoding
        $room = json_decode(app('redis')->get($roomId));
        $users = [];
        // If users key exists and users not empty
        if (isset($room->users) && !empty($room->users)) {
            foreach ($room->users as $user) {
                // Finding user
                if ($user->user_id !== $userId) {
                    // Users adding to $users data except $userId user
                    $users[] = $user;
                }
            }
        }
        // Saving new users data to room
        $room->users = $users;
        return app('redis')->set($roomId, json_encode($room)) ? app('redis')->get($roomId) : false;
    }

    public function userInRoom($roomId, $userId, $room = false)
    {
        // Getting room and json decoding
        if ($room === false)
            $room = json_decode(app('redis')->get($roomId));

        // If users key exists and users not empty
        if (isset($room->users) && !empty($room->users)) {
            foreach ($room->users as $user) {
                // Finding user
                if ($user->user_id === $userId) {
                    // If user in this room then return this room
                    return $room;
                    break;
                }
            }
        }
        return false;
    }

    public function exists($roomId)
    {
        // Getting room
        $room = app('redis')->get($roomId);
        // If room not empty (exists) return decoded room else return false
        return !empty($room) ? json_decode($room) : false;
    }

    public function userAndRoomExists($roomId, $userId)
    {
        // If room exists
        if ($room = $this->exists($roomId)) {
            // If user in this room return room else return false
            return $this->userInRoom($roomId, $userId, $room);
        }
        return false;
    }
}