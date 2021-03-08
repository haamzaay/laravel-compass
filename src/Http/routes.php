<?php

// Route request.
Route::middleware(config('compass.authenticator.middleware'))->get('/request', 'RequestController@index')->name('request');
Route::middleware(config('compass.authenticator.middleware'))->post('/request/{name?}', 'RequestController@store')->name('request.store');
Route::middleware(config('compass.authenticator.middleware'))->get('/request/{id}', 'RequestController@show')->name('request.show');
Route::middleware(config('compass.authenticator.middleware'))->get('/request/add/{id}', 'RequestController@showEmpty')->name('request.show.empty');

// Route response.
Route::middleware(config('compass.authenticator.middleware'))->post('/response', 'ResponseController@store')->name('response.store');
Route::middleware(config('compass.authenticator.middleware'))->get('/response/{uuid}', 'ResponseController@show')->name('response.show');
Route::middleware(config('compass.authenticator.middleware'))->delete('/response/{uuid}', 'ResponseController@destroy')->name('response.destroy');

Route::middleware(config('compass.authenticator.middleware'))->get('/credentials', 'CredentialController')->name('credentials');

// Catch-all Route.
Route::middleware(config('compass.authenticator.middleware'))->get('/{view?}', 'HomeController')->where('view', '(.*)')->name('home');
