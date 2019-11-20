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
Route::get('tab_name/{id}', 'Admin\QuizController@tabName');
Route::post('submit', 'Admin\QuizController@submitQuestionForm');
Route::get('get_tabs', 'Admin\QuizController@getTabs');
Route::get('questions/{id}/tab/{tab}', 'Admin\QuizController@getTabQuestions');

//Guest
Route::get('set', 'Guest\GuestController@getSetAll');
Route::get('start_quiz/{id}', 'Guest\GuestController@startQuiz');
Route::post('confirm_password', 'Guest\GuestController@confirmPassword');
Route::post('submit_quiz', 'Guest\GuestController@submitQuiz');