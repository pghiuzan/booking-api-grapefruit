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

$router->group(['middleware' => 'apiKeyAuth'], static function() use ($router) {
    $router->get('/health-check', function () {
        return response()->json([
            'status' => 'ok',
        ]);
    });

    $router->group(['prefix' => 'users'], static function() use ($router) {
        $router->get('/', ['uses' => 'UsersController@index']);
        $router->get('/{id}', ['uses' => 'UsersController@read']);
        $router->post('/', ['uses' => 'UsersController@create']);
        $router->patch('/{id}', ['uses' => 'UsersController@update']);
        $router->delete('/{id}', ['uses' => 'UsersController@delete']);
    });

    $router->group(['prefix' => 'bookings'], static function() use ($router) {
        $router->get('/', ['uses' => 'BookingsController@index']);
    });
});

$router->group(['prefix' => 'trips'], static function() use ($router) {
    $router->get('/', ['uses' => 'TripsController@index']);
    $router->get('/search', ['uses' => 'TripsController@search']);
    $router->get('/{slug}', ['uses' => 'TripsController@read']);

    $router->group(['middleware' => 'apiKeyAuth'], static function() use ($router) {
        $router->post('/', ['uses' => 'TripsController@create']);
        $router->patch('/{slug}', ['uses' => 'TripsController@update']);
        $router->delete('/{id}', ['uses' => 'TripsController@delete']);
    });
});

$router->group(['prefix' => 'auth'], static function() use ($router) {
    $router->post('/', ['uses' => 'AuthController@login']);
});

$router->group(['prefix' => 'bookings', 'middleware' => 'auth:api'], static function() use ($router) {
    $router->post('/', ['uses' => 'BookingsController@bookTrip']);
});
