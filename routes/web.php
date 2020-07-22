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
    $router->post('/', 'RoomController@store');
    $router->post('/join', 'RoomController@join');
    $router->delete('/{roomId}/user/{userId}', 'RoomController@destroy');

    $router->delete('/', 'RoomController@flush');// TODO WILL BE DELETED ON PRODUCTION
    $router->get('/', 'RoomController@all'); // TODO WILL BE DELETED ON PRODUCTION

    $router->get('/{roomId}/user/{userId}', 'RoomController@get');
});

$router->group(['prefix' => '/video-actions'], function () use ($router) {
    $router->post('/on-playing', 'VideoController@onPlaying');
});
