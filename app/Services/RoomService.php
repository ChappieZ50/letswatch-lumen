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
        return 3600 * 24; // 24 Hours
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
        $uuid = $this->createHash();
        $expire = $this->createExpire();

        $request->merge([
            'room_id' => $uuid,
        ]);

        return app('redis')->set($request->get('room_id'), json_encode([
            'room_id' => $request->get('room_id'),
            'owner'   => $request->get('user_id'),
            'users'   => [
                [
                    'username' => $request->get('username'),
                    'user_id'  => $request->get('user_id'),
                    'gender'   => $request->get('gender'),
                ]
            ],
        ]), 'ex', $expire) ? app('redis')->get($uuid) : false;
    }

}