<?php

namespace App\Services;

class ChatService
{
    protected $redis;

    public function __construct()
    {
        $this->redis = app('redis')->connection('chat');
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
            'user_id' => 'required|uuid',
            'room_id' => 'required|uuid',
            'message' => 'required|string|max:100'
        ];
    }

    public function save($store, $id, $chat)
    {
        if ($chat !== false) {
            $chat[] = $store;
            return $this->redis->set($id, json_encode($chat));
        }
        return $this->redis->set($id, json_encode([$store]), 'ex', $this->createExpire());
    }


    public function exists($id)
    {
        // Getting chat
        $chat = $this->redis->get($id);
        // If chat not empty (exists) return decoded chat else return false
        return !empty($chat) ? json_decode($chat) : false;
    }

}