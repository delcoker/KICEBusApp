<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/login', 'UsersController@postLogin');

//Route::post('transaction','TransactionController@transaction');

$router->group(['middleware' => 'web'], function() {


    // Only authenticated users may enter...

    Route::get('logout', array('as' => 'logout', 'uses' => 'UsersController@logout'));

    Route::get('settings', array('as' => 'settings', 'middleware' => 'role', 'uses' => 'UsersController@saveSettings'));



    Route::post('transaction', 'TransactionController@transaction');

    Route::get('buslocation', array('as' => 'buslocation', 'uses' => 'BusController@addBusLocation'));
});
