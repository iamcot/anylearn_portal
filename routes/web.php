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
Route::get('/ref/{code}', 'PageController@ref');
Route::post('/ref/{code}', 'Auth\RegisterController@registerRefPage');

Auth::routes();

//public page
Route::get('/privacy', 'ConfigController@privacy');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::any('/config/banner', 'ConfigController@banner')->name('config.banner');
    Route::any('/config/site', 'ConfigController@site')->name('config.site');
    Route::get('/config/banner/del/{img}', 'ConfigController@delBanner')->name('config.banner.del');
    Route::get('/config/guide/{type}', 'ConfigController@guide')->name('config.guide');
    Route::post('/config/guide/{type}', 'ConfigController@guideUpdate');
    Route::post('/upload/ckimage', 'FileController@ckEditorImage')->name('upload.ckimage');
    
    Route::any('/user/update-doc', 'UserController@updateDoc')->name('user.update_doc');
    
    Route::any('/user/mods/create', 'UserController@modCreate')->name('user.mods.create');
    Route::any('/user/mods/{userId}', 'UserController@modEdit')->name('user.mods.edit');
    Route::get('/user/mods', 'UserController@mods')->name('user.mods');

    Route::get('/user/members', 'UserController@members')->name('user.members');
    Route::any('/user/members/{userId}', 'UserController@memberEdit')->name('user.members.edit');

    Route::get('/user/status/{userId}', 'UserController@statusTouch')->name('user.status.touch');
    Route::get('/item/status/{itemId}', 'CourseController@statusTouch')->name('item.status.touch');
    Route::get('/item/type/change/{itemId}/{newType}', 'CourseController@typeChange')->name('item.type.change');

    Route::get('/course', 'CourseController@list')->name('course');
    Route::any('/course/create', 'CourseController@create')->name('course.create');
    Route::middleware('access.item')->get('/course/{id}', 'CourseController@detail')->name('course.detail');
    Route::middleware('access.item')->any('/course/{id}/edit', 'CourseController@edit')->name('course.edit');
    Route::middleware('access.resource')->get('/resource/{id}/delete', 'CourseController@resourceDelete')->name('resource.delete');

    Route::get('/notif/remind-confirm/{id}', 'CourseController@remindConfirm')->name('notif.remind_confirm');

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
});

Route::get('/inactive', 'UserController@inactivePage')->name('user.inactive');

//Ajax
Route::get('/ajax/toc', 'AjaxController@toc')->name('ajax.toc');
Route::middleware(['auth', 'access.mod'])->get('/ajax/touch-is-hot/{table}/{id}', 'AjaxController@touchIsHot')->name('ajax.touch.ishot');
Route::middleware(['auth'])->get('/ajax/doc/{userId}', 'AjaxController@userDocs')->name('ajax.user.docs');
Route::middleware(['auth'])->get('/ajax/remove-doc/{userId}', 'AjaxController@userRemoveUpdateDoc')->name('ajax.user.docs.remove');
Route::middleware(['auth'])->get('/ajax/allow-doc/{userId}', 'AjaxController@userAllowUpdateDoc')->name('ajax.user.docs.remove');
