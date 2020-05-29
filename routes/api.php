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

Route::middleware('throttle:10000,1')->get('/login', 'Apis\UserApi@login');
Route::middleware('throttle:10000,1')->get('/register', 'Apis\UserApi@register');

