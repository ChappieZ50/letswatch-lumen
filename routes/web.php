<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/', function () {

});

$router->group(['prefix' => '/room'], function () use ($router) {
    $router->get('/{roomId}/user/{userId}', 'RoomController@get');

    $router->post('/', 'RoomController@store');
    $router->post('/join', 'RoomController@join');
    $router->post('/new-player', 'RoomController@newPlayer');

    $router->delete('/{roomId}/user/{userId}', 'RoomController@destroy');
});

$router->group(['prefix' => '/chat'], function () use ($router) {
    $router->get('/', 'ChatController@get');
    $router->post('/', 'ChatController@store');
});

$router->group(['prefix' => '/video-actions'], function () use ($router) {
    $router->post('/on-playing', 'VideoController@onPlaying');
});
