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

$router->get('/', function () {
    return response()->json(getallheaders());
});

$router->group(['prefix' => 'v1'], function () use ($router) {

    $router->group(['prefix' => 'auth'], function () use ($router) {

        $router->post('/login', 'AuthController@login');
        $router->get('/logout', function () {
        });
        $router->get('/users', function () {
        });
        $router->get('/users/{uuid}', function ($uuid) {
        });
        $router->post('/users', function () {
        });
        $router->put('/users/{uuid}', function ($uuid) {
        });
        $router->delete('/users/{uuid}', function ($uuid) {
        });
        $router->get('/refresh_token', function () {
        });
    });

    $router->group(['prefix' => 'domains'], function () use ($router) {

        $router->get('/', function () {
        });
        $router->post('/', function () {
        });
        $router->delete('/{uuid}', function () {
        });
    });

    $router->group(['prefix' => 'sites'], function () use ($router) {

        $router->get('/', function () {
        });
        $router->post('/', function () {
        });
        $router->delete('/{uuid}', function () {
        });
    });

    $router->group(['prefix' => 'canaries'], function () use ($router) {

        $router->get('/', function () {
        });
        $router->get('/{uuid}', function ($uuid) {
        });
        $router->get('/{uuid}/{parameter}', function ($uuid, $parameter) {
        });
        $router->post('/', function () {
        });
        $router->delete('/{uuid}', function ($uuid) {
        });
    });

    $router->group(['prefix' => 'mail'], function () use ($router) {

        $router->get('/{uuid}', function ($uuid) {
        });
    });
});













// Fake api
$router->group(['prefix' => 'fake'], function () use ($router) {

    $router->group(['prefix' => 'auth'], function () use ($router) {

        $router->post('/login', function () {
            return response()->json(array(
                'token' => 'fr78324i76td6i74yf4y378fo2',
                'token_type' => 'bearer',
                'expires' => 3600,
                'uuid' => 'r89u247ry-r42ht8547-r42yuj876',
                'permissions' => array('admin'),
                'canaries' => array('rf4u83y8743r76tr247')
            ));
        });

        $router->get('/logout', function () {
        });

        $router->get('/users', function () {
        });

        $router->get('/users/{uuid}', function ($uuid) {
        });

        $router->post('/users', function () {
        });

        $router->put('/users/{uuid}', function ($uuid) {
        });

        $router->delete('/users/{uuid}', function ($uuid) {
        });

        $router->get('/refresh_token', function () {
        });
    });

    $router->group(['prefix' => 'domains'], function () use ($router) {

        $router->get('/', function () {
        });

        $router->post('/', function () {
        });

        $router->delete('/{uuid}', function () {
        });
    });

    $router->group(['prefix' => 'sites'], function () use ($router) {

        $router->get('/', function () {
        });

        $router->post('/', function () {
        });

        $router->delete('/{uuid}', function () {
        });
    });

    $router->group(['prefix' => 'canaries'], function () use ($router) {

        $router->get('/', function () {
        });

        $router->get('/{uuid}', function ($uuid) {
        });

        $router->get('/{uuid}/{parameter}', function ($uuid, $parameter) {
        });

        $router->post('/', function () {
        });

        $router->delete('/{uuid}', function ($uuid) {
        });
    });

    $router->group(['prefix' => 'mail'], function () use ($router) {

        $router->get('/{uuid}', function ($uuid) {
        });
    });
});
