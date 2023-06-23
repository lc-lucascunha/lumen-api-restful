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

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->group(['prefix' => 'clients'], function () use ($router) {
        $router->get('/'       , 'ClientsController@index');
        $router->post('/'      , 'ClientsController@store');
        $router->get('/{id}'   , 'ClientsController@show');
        $router->put('/{id}'   , 'ClientsController@update');
        $router->delete('/{id}', 'ClientsController@destroy');

        $router->get('/{id}/orders', 'ClientsController@showOrders');
    });

    $router->group(['prefix' => 'products'], function () use ($router) {
        $router->get('/'       , 'ProductsController@index');
        $router->post('/'      , 'ProductsController@store');
        $router->get('/{id}'   , 'ProductsController@show');
        $router->put('/{id}'   , 'ProductsController@update');
        $router->delete('/{id}', 'ProductsController@destroy');
    });

    $router->group(['prefix' => 'orders'], function () use ($router) {
        $router->get('/'       , 'OrdersController@index');
        $router->post('/'      , 'OrdersController@store');
        $router->get('/{id}'   , 'OrdersController@show');
        $router->put('/{id}'   , 'OrdersController@update');
        $router->delete('/{id}', 'OrdersController@destroy');
    });

});
