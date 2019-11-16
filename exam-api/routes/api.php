<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function(){
    Route::post('signin', 'SignInController');
    Route::post('signout', 'SignOutController');
    Route::get('test', 'DataTestController');
});

//Admin
Route::post('save_set', 'Admin\QuizController@saveSet');
Route::put('save_set/{id}', 'Admin\QuizController@updateSet');
Route::delete('save_set/{id}', 'Admin\QuizController@deleteSet');
Route::get('set_name/{id}', 'Admin\QuizController@setName');

//Guest
Route::get('set', 'Guest\GuestController@getSetAll');