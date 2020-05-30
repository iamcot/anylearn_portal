<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', 'Apis\UserApi@userInfo');

Route::middleware('throttle:20,1')->get('/login', 'Apis\UserApi@login');
Route::get('/users/{role}', 'Apis\UserApi@usersList');
Route::get('/friends/{userId}', 'Apis\UserApi@friends');
Route::middleware('throttle:10,1')->post('/register', 'Apis\UserApi@register');
Route::middleware('throttle:10,1')->post('/user/edit', 'Apis\UserApi@edit');
Route::middleware('throttle:10,1')->post('/user/upload-image/{type}', 'Apis\UserApi@uploadImage');

Route::get('/config/home/{role}', 'Apis\ConfigApi@home');
