<?php

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

$router->get('/', function () use ($router) {
    return "
        <h1>Scania Tracker</h1>
        <h4>API de geolocalização de veículos</h4>
        <h5>{$router->app->version()}</h5>";
});

// Authentication
$router->post('auth/login', 'AuthController@login');
$router->post('auth/forgot-password', 'AuthController@forgotPassword');

// Authenticated area
$router->group(['middleware' => 'auth:api'], function () use ($router) {
    // AuthController
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->get('/me', 'AuthController@me');
        $router->post('/logout', 'AuthController@logout');
        $router->post('/refresh', 'AuthController@refresh');
        $router->patch('/change-password', 'AuthController@changePassword');
    });

    $router->group(['prefix' =>'user'], function () use ($router) {
        $router->post('/', 'UserController@create');
        $router->get('/{id:[0-9]+}', 'UserController@read');
        $router->patch('/{id:[0-9]+}', 'UserController@update');
        $router->delete('/{id:[0-9]+}', 'UserController@delete');
        $router->get('/', 'UserController@list');
    });

    $router->get('/last-position/{numberPlate}', 'HomeController@getLastPosition');
});
