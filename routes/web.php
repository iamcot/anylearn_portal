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

Auth::routes();

Route::middleware(['auth'])->group(function () {
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

    Route::get('/course', 'CourseController@list')->name('course');
    Route::any('/course/create', 'CourseController@create')->name('course.create');
    Route::get('/course/{courseId}', 'CourseController@detail')->name('course.detail');
    Route::any('/course/{courseId}/edit', 'CourseController@edit')->name('course.edit');
});

Route::get('/inactive', 'UserController@inactivePage')->name('user.inactive');

//Ajax
Route::get('/ajax/toc', 'AjaxController@toc')->name('ajax.toc');
Route::middleware(['auth'])->get('/ajax/doc/{userId}', 'AjaxController@userDocs')->name('ajax.user.docs');
Route::middleware(['auth'])->get('/ajax/remove-doc/{userId}', 'AjaxController@userRemoveUpdateDoc')->name('ajax.user.docs.remove');
