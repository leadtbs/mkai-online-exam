<?php

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function(){
    Route::post('signin', 'SignInController');
    Route::get('test', 'DataTestController');
    Route::post('signout', 'SignOutController');
});

//Admin
Route::post('save_set', 'Admin\QuizController@saveSet');
Route::put('save_set/{id}', 'Admin\QuizController@updateSet');
Route::delete('save_set/{id}', 'Admin\QuizController@deleteSet');
Route::get('set_name/{id}', 'Admin\QuizController@setName');
Route::get('tab_name/{id}', 'Admin\QuizController@tabName');
Route::post('submit', 'Admin\QuizController@submitQuestionForm');
Route::post('update-question', 'Admin\QuizController@updateQuestion');
Route::delete('delete-question/{id}', 'Admin\QuizController@deleteQuestion');
Route::get('get_tabs', 'Admin\QuizController@getTabs');
Route::get('questions/{id}/tab/{tab}', 'Admin\QuizController@getTabQuestions');
Route::get('get-questions-and-choices/{question_id}', 'Admin\QuizController@getQuestionsAndChoices');

//Guest
Route::get('set', 'Guest\GuestController@getSetAll');
Route::get('start_exam/{id}', 'Guest\GuestController@startExam');
Route::post('confirm-password', 'Guest\GuestController@confirmPassword');
Route::post('submit_exam', 'Guest\GuestController@submitExam');