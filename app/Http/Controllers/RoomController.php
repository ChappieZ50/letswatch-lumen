<?php

namespace App\Http\Controllers;

use App\Helper;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    /**
     * @param Helper $helper
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Helper $helper)
    {
        $uuid = $helper->createHash();
        $expire = $helper->createExpire();
        $visitor = $helper->createVisitor();

        $set = app('redis')->set($uuid, json_encode([
            'user'    => $visitor,
            'room_id' => $uuid
        ]), $expire);

        return $set ? app('redis')->get($uuid) :
            response()->json([
                'message' => 'Room not created'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function get($uuid)
    {
        return app('redis')->get($uuid);
    }
}
