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

/*Route::get('/login', 'Apis\UserApi@login');
Route::post('/login/facebook', 'Apis\UserApi@loginFacebook');
Route::post('/login/apple', 'Apis\UserApi@loginApple');
Route::get('/logout', 'Apis\UserApi@logout');
Route::post('/register', 'Apis\UserApi@register');
Route::any('/simple-register', 'Apis\UserApi@simpleRegister');
Route::get('/password/otp', 'Apis\UserApi@sentOtpResetPass');
Route::post('/password/reset', 'Apis\UserApi@resetPassOtp');
Route::post('/otp/check', 'Apis\UserApi@otpCheck');

Route::get('/users/{role}', 'Apis\UserApi@usersList');
Route::get('/user/{userId}/items', 'Apis\ItemApi@userItems');
Route::get('/user/profile/{userId}', 'Apis\UserApi@profile');

Route::get('/event/{month}', 'Apis\ConfigApi@event');
Route::get('/search', 'Apis\ConfigApi@search');
Route::get('/search-tags', 'Apis\ConfigApi@searchTags');
Route::get('/config/home/{role}', 'Apis\ConfigApi@home');
Route::get('/config/homev2/{role}', 'Apis\ConfigApi@homeV2');
Route::get('/config/category/{catId?}', 'Apis\ConfigApi@category');
Route::get('/pdp/{id}', 'Apis\ItemApi@pdp');
Route::get('/foundation', 'Apis\ConfigApi@foundation');
Route::get('/doc/{key}', 'Apis\ConfigApi@getDoc');
Route::get('/item/{itemId}/reviews', 'Apis\ItemApi@reviews');

Route::get('/article', 'Apis\ArticleApi@index');
Route::get('/article/cat/{type}', 'Apis\ArticleApi@loadByType');
Route::get('/article/{id}', 'Apis\ArticleApi@loadArticle');

Route::get('/quote', 'Apis\ArticleApi@quote');

Route::get('/ask/list', 'Apis\AskApi@getList');
Route::get('/ask/{askId}', 'Apis\AskApi@getThread');

Route::post('/report/ecommerce', 'Apis\ConfigApi@reportEcommerce');

Route::middleware(['language'])->group(function () {
    Route::get('/pdp/{id}', 'Apis\ItemApi@pdp');
    Route::get('/config/category/{catId?}', 'Apis\ConfigApi@category');
    Route::get('/user/{userId}/items', 'Apis\ItemApi@userItems');

    // Route::get('/article', 'Apis\ArticleApi@index');
    // Route::get('/article/cat/{type}', 'Apis\ArticleApi@loadByType');
    // Route::get('/article/{id}', 'Apis\ArticleApi@loadArticle');
    // Route::get('/quote', 'Apis\ArticleApi@quote');
// Route::get('/user/profile/{userId}', 'Apis\UserApi@profile');

});

Route::get('/social/profile/{userId}', 'Apis\SocialController@profile');
Route::get('/social/post/{postId}', 'Apis\SocialController@post');

Route::middleware(['api.user'])->group(function () {
    Route::get('/social/profile', 'Apis\SocialController@profile');
    Route::any('/social/{postId}/action', 'Apis\SocialController@action');

    Route::get('/user', 'Apis\UserApi@userInfo');
    Route::get('/user-less', 'Apis\UserApi@userInfoLess');
    Route::get('/friends/{userId}', 'Apis\UserApi@friends');
    Route::post('/user/edit', 'Apis\UserApi@edit');
    Route::post('/user/upload-image/{type}', 'Apis\UserApi@uploadImage');
    Route::get('/user/mycalendar', 'Apis\UserApi@myCalendar');
    Route::get('/user/join/{itemId}', 'Apis\UserApi@confirmJoinCourse');
    Route::get('/user/course-registered-users/{itemId}', 'Apis\UserApi@courseRegisteredUsers');
    Route::get('/user/get-docs', 'Apis\UserApi@getDocs');
    Route::post('/user/add-doc', 'Apis\UserApi@addDoc');
    Route::get('/user/remove-doc/{fileId}', 'Apis\UserApi@removeDoc');
    Route::get('/user/notification', 'Apis\UserApi@notification');
    Route::get('/user/notification/{id}', 'Apis\UserApi@notifRead');
    Route::get('/user/all-friends', 'Apis\UserApi@allFriends');
    Route::get('/user/contract/{contractId?}', 'Apis\UserApi@getContract');
    Route::any('/user/contract', 'Apis\UserApi@saveContract');
    Route::any('/user/contract/sign/{contractId}', 'Apis\UserApi@signContract');
    Route::post('/user/changepass', 'Apis\UserApi@changePass');
    Route::get('/user/delete', 'Apis\UserApi@deleteAccount');
    Route::get('/user/pending-orders', 'Apis\UserApi@pendingOrders');

    Route::get('/transaction/history', 'Apis\TransactionApi@history');
    Route::post('/transaction/deposit', 'Apis\TransactionApi@saveDeposit');
    Route::post('/transaction/exchange', 'Apis\TransactionApi@saveExchange');
    Route::post('/transaction/withdraw', 'Apis\TransactionApi@saveWithdraw');
    Route::get('/transaction/register/{itemId}', 'Apis\TransactionApi@placeOrderOneItem');

    Route::post('/item/create', 'Apis\ItemApi@create');
    Route::get('/item/{id}/edit', 'Apis\ItemApi@edit');
    Route::post('/item/{id}/edit', 'Apis\ItemApi@save');
    Route::get('/item/list', 'Apis\ItemApi@list');
    Route::post('/item/{itemId}/upload-image', 'Apis\ItemApi@uploadImage');
    Route::get('/item/{itemId}/user-status/{newStatus}', 'Apis\ItemApi@changeUserStatus');
    Route::post('/item/{id}/share', 'Apis\ItemApi@share');

    Route::get('/config/transaction/{type}', 'Apis\ConfigApi@transaction');
    Route::post('/config/feedback', 'Apis\ConfigApi@saveFeedback');

    Route::get('/item/{itemId}/touch-fav', 'Apis\ItemApi@touchFav');
    Route::post('/item/{itemId}/save-rating', 'Apis\ItemApi@saveRating');

    Route::post('/ask/create/{type}', 'Apis\AskApi@create');
    Route::post('/ask/{askId}/edit', 'Apis\AskApi@edit');
    Route::get('/ask/{askId}/select', 'Apis\AskApi@selectAnswer');
    Route::get('/ask/{askId}/vote/{type}', 'Apis\AskApi@vote');
    Route::get('/ask/{askId}/touch/{status}', 'Apis\AskApi@touchStatus');

    Route::get('/user/children', 'Apis\UserApi@listChildren');
    Route::post('/user/children', 'Apis\UserApi@saveChildren');
    Route::post('/user/childrenv2', 'Apis\UserApi@saveChildrenV2');

});*/

// Testing v3
Route::prefix('v3')->group(function () {
    Route::get('/login', 'Apis\UserApi@login');
    Route::post('/login/facebook', 'Apis\UserApi@loginFacebook');
    Route::post('/login/apple', 'Apis\UserApi@loginApple');
    Route::get('/logout', 'Apis\UserApi@logout');
    Route::post('/register', 'Apis\UserApi@register');
    Route::any('/simple-register', 'Apis\UserApi@simpleRegister');
    Route::get('/password/otp', 'Apis\UserApi@sentOtpResetPass');
    Route::post('/password/reset', 'Apis\UserApi@resetPassOtp');
    Route::post('/otp/check', 'Apis\UserApi@otpCheck');

    Route::get('/users/{role}', 'Apis\UserApi@usersList');
    Route::get('/user/{userId}/items', 'Apis\ItemApi@userItems');
    Route::get('/user/profile/{userId}', 'Apis\UserApi@profile');

    Route::get('/event/{month}', 'Apis\ConfigApi@event');
    Route::get('/search', 'Apis\ConfigApi@search');
    Route::get('/search-tags', 'Apis\ConfigApi@searchTags');
    Route::get('/config/home/{role}', 'Apis\ConfigApi@home');
    Route::get('/config/homev2/{role}', 'Apis\ConfigApi@homeV2');
    Route::get('/config/category/{catId?}', 'Apis\ConfigApi@category');
    Route::get('/pdp/{id}', 'Apis\ItemApi@pdp');
    Route::get('/foundation', 'Apis\ConfigApi@foundation');
    Route::get('/doc/{key}', 'Apis\ConfigApi@getDoc');
    Route::get('/item/{itemId}/reviews', 'Apis\ItemApi@reviews');

    Route::get('/article', 'Apis\ArticleApi@index');
    Route::get('/article/cat/{type}', 'Apis\ArticleApi@loadByType');
    Route::get('/article/{id}', 'Apis\ArticleApi@loadArticle');

    Route::get('/quote', 'Apis\ArticleApi@quote');

    Route::get('/ask/list', 'Apis\AskApi@getList');
    Route::get('/ask/{askId}', 'Apis\AskApi@getThread');

    Route::post('/report/ecommerce', 'Apis\ConfigApi@reportEcommerce');

    Route::get('/social/profile/{userId}', 'Apis\SocialController@profile');
    Route::get('/social/post/{postId}', 'Apis\SocialController@post');

    Route::middleware(['language'])->group(function () {
        Route::get('/pdp/{id}', 'Apis\ItemApi@pdp');
        Route::get('/config/category/{catId?}', 'Apis\ConfigApi@category');
        Route::get('/user/{userId}/items', 'Apis\ItemApi@userItems');
    });

    Route::middleware(['api.user'])->group(function () {
        Route::get('/home/{role}', 'Apis\ConfigApi@homeV3');
        
        Route::get('/social/profile', 'Apis\SocialController@profile');
        Route::any('/social/{postId}/action', 'Apis\SocialController@action');

        Route::get('/user', 'Apis\UserApi@userInfo');
        Route::get('/user-less', 'Apis\UserApi@userInfoLess');
        Route::get('/friends/{userId}', 'Apis\UserApi@friends');
        Route::post('/user/edit', 'Apis\UserApi@edit');
        Route::post('/user/upload-image/{type}', 'Apis\UserApi@uploadImage');
        Route::get('/user/mycalendar', 'Apis\UserApi@myCalendar');
        Route::get('/user/join/{itemId}', 'Apis\UserApi@confirmJoinCourse');
        Route::get('/user/course-registered-users/{itemId}', 'Apis\UserApi@courseRegisteredUsers');
        Route::get('/user/get-docs', 'Apis\UserApi@getDocs');
        Route::post('/user/add-doc', 'Apis\UserApi@addDoc');
        Route::get('/user/remove-doc/{fileId}', 'Apis\UserApi@removeDoc');
        Route::get('/user/notification', 'Apis\UserApi@notification');
        Route::get('/user/notification/{id}', 'Apis\UserApi@notifRead');
        Route::get('/user/all-friends', 'Apis\UserApi@allFriends');
        Route::get('/user/contract/{contractId?}', 'Apis\UserApi@getContract');
        Route::any('/user/contract', 'Apis\UserApi@saveContract');
        Route::any('/user/contract/sign/{contractId}', 'Apis\UserApi@signContract');
        Route::post('/user/changepass', 'Apis\UserApi@changePass');
        Route::get('/user/delete', 'Apis\UserApi@deleteAccount');
        Route::get('/user/pending-orders', 'Apis\UserApi@pendingOrders');

        Route::get('/transaction/history', 'Apis\TransactionApi@history');
        Route::post('/transaction/deposit', 'Apis\TransactionApi@saveDeposit');
        Route::post('/transaction/exchange', 'Apis\TransactionApi@saveExchange');
        Route::post('/transaction/withdraw', 'Apis\TransactionApi@saveWithdraw');
        Route::get('/transaction/register/{itemId}', 'Apis\TransactionApi@placeOrderOneItem');

        Route::post('/item/create', 'Apis\ItemApi@create');
        Route::get('/item/{id}/edit', 'Apis\ItemApi@edit');
        Route::post('/item/{id}/edit', 'Apis\ItemApi@save');
        Route::get('/item/list', 'Apis\ItemApi@list');
        Route::post('/item/{itemId}/upload-image', 'Apis\ItemApi@uploadImage');
        Route::get('/item/{itemId}/user-status/{newStatus}', 'Apis\ItemApi@changeUserStatus');
        Route::post('/item/{id}/share', 'Apis\ItemApi@share');

        Route::get('/config/transaction/{type}', 'Apis\ConfigApi@transaction');
        Route::post('/config/feedback', 'Apis\ConfigApi@saveFeedback');

        Route::get('/item/{itemId}/touch-fav', 'Apis\ItemApi@touchFav');
        Route::post('/item/{itemId}/save-rating', 'Apis\ItemApi@saveRating');

        Route::post('/ask/create/{type}', 'Apis\AskApi@create');
        Route::post('/ask/{askId}/edit', 'Apis\AskApi@edit');
        Route::get('/ask/{askId}/select', 'Apis\AskApi@selectAnswer');
        Route::get('/ask/{askId}/vote/{type}', 'Apis\AskApi@vote');
        Route::get('/ask/{askId}/touch/{status}', 'Apis\AskApi@touchStatus');

        Route::get('/user/children', 'Apis\UserApi@listChildren');
        Route::post('/user/children', 'Apis\UserApi@saveChildren');
        Route::post('/user/childrenv2', 'Apis\UserApi@saveChildrenV2');

        //--v3
        Route::get('/user/point-box', 'Apis\UserApi@pointBox');

    });
});