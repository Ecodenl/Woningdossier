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


Route::domain('{cooperation}.' . config('woningdossier.domain'))->group(function(){

	Route::group(['middleware' => 'cooperation', 'as' => 'cooperation.', 'namespace' => 'Cooperation'], function() {
		Route::get('/', function() { return view( 'cooperation.welcome' ); })->name('welcome');
		Auth::routes();
		Route::get( '/confirm',
			'Auth\RegisterController@confirm' )->name( 'confirm' );

		Route::get( '/home', 'HomeController@index' )->name( 'home' );
	});

});

Route::get('/', function () {
	return view('welcome');
})->name('index');