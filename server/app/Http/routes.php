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


$app->post('game', 'GameController@post');
$app->get('game/filter', 'GameController@filter');
$app->get('games', 'GameController@getList');
$app->post('game/parse', 'GameController@post_async');
$app->post('game/refresh', 'GameController@refresh_async');
$app->get('game/{id}', 'GameController@getGame');
$app->get('games/last', 'GameController@getLastList');
$app->get('game/refresh/{id}', 'GameController@refresh');
$app->post('game/find', 'GameController@find');
$app->post('alert', 'GameController@alert');
