<?php

namespace App\Http\Controllers;

use App\Services\RoomService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomController extends Controller
{
    protected $defaultErrorMessage = 'Failed to create room';

    public function store(RoomService $service, Request $request)
    {
        $this->validate($request, $service->rules());

        $saved = $service->save($request);

        return !$saved ? response()->json([
            'message' => $this->defaultErrorMessage
        ], Response::HTTP_INTERNAL_SERVER_ERROR) : response()->json($saved);

    }

    public function get($uuid)
    {
        return response()->json(app('redis')->get($uuid));
    }

    public function all()
    {
        return app('redis')->keys('*');
    }

    public function destroy()
    {
        return app('redis')->flushdb();
    }
}
