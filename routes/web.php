<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

/**
 * Routes - /api/v1/games
 */
Route::group([
    'prefix'     => '/api/v1/games',
    'middleware' => 'web'
], function () {
    Route::get('/', 'SiteController@getAllGames')->name('get.all.games');
    Route::post('/', 'SiteController@startNewGame');
    Route::get('/{game_id}', 'SiteController@getGame')->name('get.game');
    Route::put('/{game_id}', 'SiteController@moveGame');
    Route::delete('/{game_id}', 'SiteController@deleteGame')->name('delete.game');
});