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
    return $router->app->version();
});

$router->get('/veiculo[/{numberPlate}]', 'HomeController@getVehicle');
$router->get('/veiculo/{numberPlate}/position', 'HomeController@getPosition');
$router->get('/last-position', 'HomeController@getLasPosition');