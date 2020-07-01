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
    $router->post('', 'RoomController@store');
    $router->get('/', 'RoomController@all');
    $router->get('/{room_id}/user/{user_id}', 'RoomController@get');
    $router->delete('/', 'RoomController@destroy'); // TODO Only on Local
});
