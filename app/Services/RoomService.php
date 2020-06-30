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
            'uuid'      => 'required|uuid',
            'gender'    => 'required|in:male,female'
        ];
    }

    public function save(Request $request)
    {
        $uuid = $this->createHash();
        $expire = $this->createExpire();

        return app('redis')->set($uuid, json_encode([
            'user'    => $request->get('username'),
            'room_id' => $uuid,
            'user_id' => $request->get('uuid'),
            'gender'  => $request->get('gender'),
            'users'   => []
        ]), 'ex', $expire) ? app('redis')->get($uuid) : false;
    }

}