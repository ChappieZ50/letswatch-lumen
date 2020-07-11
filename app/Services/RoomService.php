<?php

namespace App\Services;

use App\Rules\RecaptchaRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomService
{

    public function createHash()
    {
        return Str::uuid();
    }

    public function createExpire()
    {
        return 3600 * 12; // 24 Hours
    }

    public function rules()
    {
        return [
            'username'  => 'required|max:15|min:3|string',
            'recaptcha' => ['required', 'string', new RecaptchaRule()],
            'user_id'   => 'required|uuid',
            'gender'    => 'required|in:male,female'
        ];
    }

    public function save(Request $request)
    {
        $room_id = $this->createHash();
        $expire = $this->createExpire();

        return app('redis')->set($room_id, json_encode([
            'room_id'  => $room_id,
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
                'url'  => 'https://youtu.be/UjtOGPJ0URM',
                'type' => 'youtube',
                'seek' => 0
            ]
        ]), 'ex', $expire) ? app('redis')->get($room_id) : false;
    }

    public function userInRoom($roomID, $userID)
    {
        $room = json_decode(app('redis')->get($roomID));
        if (isset($room->users) && !empty($room->users)) {
            foreach ($room->users as $user) {
                if ($user->user_id === $userID) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function exists($roomId)
    {
        $room = app('redis')->get($roomId);
        return !empty($room) ? json_decode($room) : false;
    }
}