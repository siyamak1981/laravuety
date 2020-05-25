<?php

use Illuminate\Support\Facades\Route;

// Auth::routes();

// Route::get('/{any}', 'HomeController@index')->where('any', '.*');
Route::view('/{any}', 'home')->where('any', '.*');
