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

Route::get('/login', 'Apis\UserApi@login');
Route::get('/users/{role}', 'Apis\UserApi@usersList');

Route::get('/friends/{userId}', 'Apis\UserApi@friends');
Route::post('/register', 'Apis\UserApi@register');
Route::post('/user/edit', 'Apis\UserApi@edit');
Route::post('/user/upload-image/{type}', 'Apis\UserApi@uploadImage');
Route::get('/user/{userId}/items', 'Apis\ItemApi@userItems');

Route::get('/config/home/{role}', 'Apis\ConfigApi@home');
Route::get('/config/transaction/{type}', 'Apis\ConfigApi@transaction');

Route::post('/item/create', 'Apis\ItemApi@create');
Route::get('/item/{id}/edit', 'Apis\ItemApi@edit');
Route::post('/item/{id}/edit', 'Apis\ItemApi@save');
Route::get('/item/list', 'Apis\ItemApi@list');
Route::post('/item/{itemId}/upload-image', 'Apis\ItemApi@uploadImage');
Route::get('/item/{itemId}/user-status/{newStatus}', 'Apis\ItemApi@changeUserStatus');
Route::get('/pdp/{id}', 'Apis\ItemApi@pdp');

