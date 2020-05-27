<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::get('signup/activate/{token}', 'AuthController@signupActivate');
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::get('/varify', 'AuthController@varify');
        Route::resource('roles', 'RoleController');
        Route::post('roles/delete', 'RoleController@deleteAll');
        Route::resource('users', 'UserController');
        Route::post('users/delete', 'UserController@deleteAll');
        Route::post('user/role', 'UserController@changeRole');
        Route::post('email/validate', 'UserController@varifyEmail');
        Route::post('user/photo', 'UserController@changePhoto');
    });
});
Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});
