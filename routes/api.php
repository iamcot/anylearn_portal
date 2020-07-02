<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/user-less', 'Apis\UserApi@userInfoLess');

Route::get('/login', 'Apis\UserApi@login');
Route::get('/logout', 'Apis\UserApi@logout');
Route::get('/users/{role}', 'Apis\UserApi@usersList');

Route::get('/friends/{userId}', 'Apis\UserApi@friends');
Route::post('/register', 'Apis\UserApi@register');
Route::post('/user/edit', 'Apis\UserApi@edit');
Route::post('/user/upload-image/{type}', 'Apis\UserApi@uploadImage');
Route::get('/user/{userId}/items', 'Apis\ItemApi@userItems');
Route::get('/user/mycalendar', 'Apis\UserApi@myCalendar');
Route::get('/user/join/{itemId}', 'Apis\UserApi@confirmJoinCourse');
Route::get('/user/course-registered-users/{itemId}', 'Apis\UserApi@courseRegisteredUsers');
Route::get('/user/profile/{userId}', 'Apis\UserApi@profile');
Route::get('/user/get-docs', 'Apis\UserApi@getDocs');
Route::post('/user/add-doc', 'Apis\UserApi@addDoc');
Route::get('/user/remove-doc/{fileId}', 'Apis\UserApi@removeDoc');
Route::get('/user/notification', 'Apis\UserApi@notification');
Route::get('/user/notification/{id}', 'Apis\UserApi@notifRead');

Route::get('/config/home/{role}', 'Apis\ConfigApi@home');
Route::get('/config/transaction/{type}', 'Apis\ConfigApi@transaction');
Route::get('/foundation', 'Apis\ConfigApi@foundation');
Route::get('/doc/{key}', 'Apis\ConfigApi@getDoc');
Route::post('/config/feedback', 'Apis\ConfigApi@saveFeedback');

Route::post('/item/create', 'Apis\ItemApi@create');
Route::get('/item/{id}/edit', 'Apis\ItemApi@edit');
Route::post('/item/{id}/edit', 'Apis\ItemApi@save');
Route::get('/item/list', 'Apis\ItemApi@list');
Route::post('/item/{itemId}/upload-image', 'Apis\ItemApi@uploadImage');
Route::get('/item/{itemId}/user-status/{newStatus}', 'Apis\ItemApi@changeUserStatus');
Route::get('/pdp/{id}', 'Apis\ItemApi@pdp');

Route::post('/transaction/deposit', 'Apis\TransactionApi@saveDeposit');
Route::get('/transaction/history', 'Apis\TransactionApi@history');

Route::get('/transaction/register/{itemId}', 'Apis\TransactionApi@placeOrderOneItem');

Route::get('/event/{month}', 'Apis\ConfigApi@event');
