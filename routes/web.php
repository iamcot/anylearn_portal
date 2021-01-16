<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', 'PageController@home');
Route::get('/ref/{code}', 'PageController@ref')->name('refpage');
Route::post('/ref/{code}', 'Auth\RegisterController@registerRefPage');

//public page
Route::get('/privacy', 'ConfigController@privacy');

Route::get('/login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('/login/facebook/callback', 'Auth\LoginController@handleFacebookCallback');

Auth::routes();

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::any('/config/banner', 'ConfigController@banner')->name('config.banner');
    Route::any('/config/site', 'ConfigController@site')->name('config.site');
    Route::get('/config/banner/del/{img}', 'ConfigController@delBanner')->name('config.banner.del');
    Route::get('/config/guide/{type}', 'ConfigController@guide')->name('config.guide');
    Route::any('/config/homepopup', 'ConfigController@homePopup')->name('config.homepopup');
    Route::post('/config/guide/{type}', 'ConfigController@guideUpdate');
    Route::post('/upload/ckimage', 'FileController@ckEditorImage')->name('upload.ckimage');
    
    Route::any('/config/voucher', 'ConfigController@voucher')->name('config.voucher');
    Route::any('/config/voucher/create', 'ConfigController@voucherEdit')->name('config.voucher.create');
    Route::any('/config/voucher/{id}', 'ConfigController@voucherEdit')->name('config.voucher.edit');
    Route::any('/config/voucher/{id}/list', 'ConfigController@voucherList')->name('config.voucher.list');
    Route::any('/config/voucher/{type}/{id}/close', 'ConfigController@voucherClose')->name('config.voucher.close');

    Route::get('/config/voucher-event', 'ConfigController@voucherEvent')->name('config.voucherevent');
    Route::post('/config/voucher-event/create', 'ConfigController@voucherEventEdit')->name('config.voucherevent.create');
    Route::get('/config/voucher-event/{id}', 'ConfigController@voucherEventEdit')->name('config.voucherevent.edit');
    Route::get('/config/voucher-event/{id}/log', 'ConfigController@voucherEventLog')->name('config.voucherevent.log');
    Route::any('/config/voucher-event/{id}/close', 'ConfigController@voucherEventClose')->name('config.voucherevent.close');
   
    Route::any('/user/update-doc', 'UserController@updateDoc')->name('user.update_doc');
    
    Route::any('/user/mods/create', 'UserController@modCreate')->name('user.mods.create');
    Route::any('/user/mods/{userId}', 'UserController@modEdit')->name('user.mods.edit');
    Route::get('/user/mods', 'UserController@mods')->name('user.mods');

    Route::get('/user/members', 'UserController@members')->name('user.members');
    Route::any('/user/members/{userId}', 'UserController@memberEdit')->name('user.members.edit');
    Route::get('/user/contract', 'UserController@contractList')->name('user.contract');
    Route::any('/user/contract/{id}', 'UserController@contractInfo')->name('user.contract.info');

    Route::any('/user/no-profile', 'UserController@userNoProfile')->name('user.noprofile');
    Route::any('/user/remind-profile/{userId}', 'UserController@remindProfile')->name('user.noprofile.remind');

    Route::get('/user/status/{userId}', 'UserController@statusTouch')->name('user.status.touch');
    Route::get('/item/status/{itemId}', 'CourseController@statusTouch')->name('item.status.touch');
    Route::get('/item/type/change/{itemId}/{newType}', 'CourseController@typeChange')->name('item.type.change');

    Route::get('/course', 'CourseController@list')->name('course');
    Route::any('/course/create', 'CourseController@create')->name('course.create');
    Route::middleware('access.item')->get('/course/{id}', 'CourseController@detail')->name('course.detail');
    Route::middleware('access.item')->any('/course/{id}/edit', 'CourseController@edit')->name('course.edit');
    Route::middleware('access.resource')->get('/resource/{id}/delete', 'CourseController@resourceDelete')->name('resource.delete');

    Route::get('/notif/remind-confirm/{id}', 'CourseController@remindConfirm')->name('notif.remind_confirm');
    Route::get('/notif/remind-join/{id}', 'CourseController@remindJoin')->name('notif.remind_join');

    Route::get('/class', 'ClassController@list')->name('class');
    Route::any('/class/create', 'ClassController@create')->name('class.create');
    Route::middleware('access.item')->get('/class/{id}', 'ClassController@detail')->name('class.detail');
    Route::middleware('access.item')->any('/class/{id}/edit', 'ClassController@edit')->name('class.edit');
    Route::middleware('access.item')->any('/class/{id}/del-schedule', 'ClassController@delSchedule')->name('class.del.schedule');

    Route::get('/confirm', 'Controller@developing')->name('confirm');
    Route::get('/product', 'Controller@developing')->name('product');
    Route::get('/order', 'Controller@developing')->name('order');

    Route::get('/feedback', 'DashboardController@feedback')->name('feedback');

    Route::get('/transaction', 'TransactionController@transaction')->name('transaction');
    Route::get('/transaction/commission', 'TransactionController@commission')->name('transaction.commission');
    Route::get('/transaction/{id}/status/{status}', 'TransactionController@status')->name('transaction.status.touch');
    
    Route::get('/article', 'ArticleController@list')->name('article');
    Route::post('/article/create', 'ArticleController@create')->name('article.create');
    Route::any('/article/{id}', 'ArticleController@edit')->name('article.edit');
    Route::get('/article/status/{articleId}', 'ArticleController@statusTouch')->name('article.status.touch');
});

Route::get('/inactive', 'UserController@inactivePage')->name('user.inactive');

//Ajax
Route::get('/ajax/toc', 'AjaxController@toc')->name('ajax.toc');
Route::middleware(['auth', 'access.mod'])->get('/ajax/touch-is-hot/{table}/{id}', 'AjaxController@touchIsHot')->name('ajax.touch.ishot');
Route::middleware(['auth'])->get('/ajax/doc/{userId}', 'AjaxController@userDocs')->name('ajax.user.docs');
Route::middleware(['auth'])->get('/ajax/remove-doc/{userId}', 'AjaxController@userRemoveUpdateDoc')->name('ajax.user.docs.remove');
Route::middleware(['auth'])->get('/ajax/allow-doc/{userId}', 'AjaxController@userAllowUpdateDoc')->name('ajax.user.docs.remove');
