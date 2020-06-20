<?php

namespace App\Services;

use App\Rules\RecaptchaRule;
use Illuminate\Support\Str;

class RoomService
{

    public function createHash()
    {
        return Str::uuid();
    }

    public function createExpire()
    {
        return 60 * 60 * 12;
    }

    public function rules()
    {
        return [
            'username'  => 'required|max:15|min:3|string',
            'recaptcha' => ['required', 'string', new RecaptchaRule()],
        ];
    }

    public function save($username)
    {
        $uuid = $this->createHash();
        $expire = $this->createExpire();

        return app('redis')->set($uuid, json_encode([
            'user'    => $username,
            'room_id' => $uuid,
            'users'   => []
        ]), $expire) ? app('redis')->get($uuid) : false;
    }

}