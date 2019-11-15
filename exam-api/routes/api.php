<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function(){
    Route::post('signin', 'SignInController');
    Route::post('signout', 'SignOutController');
    Route::get('test', 'DataTestController');
});

Route::post('save_set', 'Admin\QuizController@saveSet');

Route::get('set', 'Guest\GuestController@getSetAll');