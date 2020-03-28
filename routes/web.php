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

        $router->group(['middleware' => 'throttle:10,1'], function () use ($router) {
            $router->post('/login', 'AuthController@login');
        });

        $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
            $router->get('/logout', 'AuthController@logout');

            $router->group(['middleware' => 'perm:admin'], function () use ($router) {
                $router->get('/users', 'UserController@listUsers');
                $router->post('/users', 'UserController@createUsers');
                $router->put('/users/{uuid}', 'UserController@modifyUser');
                $router->delete('/users/{uuid}', 'UserController@deleteUser');
            });
            $router->get('/users/{uuid}', 'UserController@getUser');

            $router->group(['middleware' => 'throttle:60,1'], function () use ($router) {
                $router->get('/refresh_token', 'AuthController@refresh');
            });
        });
    });

    $router->group(['middleware' => 'jwt.auth'], function () use ($router) {

        $router->group(['middleware' => 'perm:admin'], function () use ($router) {

            $router->group(['prefix' => 'domains'], function () use ($router) {
                $router->get('/', 'DomainController@get');
                $router->post('/', 'DomainController@add');
                $router->delete('/{uuid}', 'DomainController@delete');
            });

            $router->group(['prefix' => 'sites'], function () use ($router) {
                $router->get('/', 'SiteController@get');
                $router->post('/', 'SiteController@add');
                $router->delete('/{uuid}', 'SiteController@delete');
            });
        });

        $router->group(['prefix' => 'canaries'], function () use ($router) {
            $router->get('/', 'CanaryController@listCanaries');
            $router->get('/{uuid}', function ($uuid) {
            });
            $router->get('/{uuid}/{parameter}', function ($uuid, $parameter) {
            });
            $router->post('/{uuid}/{parameter}', function ($uuid, $parameter) {
            });
            $router->delete('/{uuid}/{parameter}', function ($uuid, $parameter) {
            });

            $router->group(['middleware' => 'perm:admin'], function () use ($router) {
                $router->post('/', 'CanaryController@add');
                $router->delete('/{uuid}', function ($uuid) {
                });
            });
        });

        $router->group(['prefix' => 'mail'], function () use ($router) {
            $router->get('/{uuid}', function ($uuid) {
            });
        });
    });
});
