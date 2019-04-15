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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Route::get('/', 'PostController@index');
// 
Route::get('/posts', 'PostController@index')->name('list_posts');
Route::group(['prefix' => 'posts'], function () {
    //  name :布局中直接生成链接.
    // 中间件认证后的用户可以访问
    // create-post 这些是策略的名字,会通过中间件去调用相应的策略.
    // 所以,策略是关键.
    // ,post 这个是注入的 post类实例.
    // 整理一下 授权这块, 第一步在auth服务者中定义策略,
    // 第二步,在路由中使用can,或者在控制器中使用Gate来鉴定权利.
    // 用户的角色或者权限,都可以在策略中定义,针对当前用户,或者针对一组用户都可以.
    // 这就是 Gate的玩法.
    Route::get('/drafts', 'PostController@drafts')
        ->name('list_drafts') 
        ->middleware('auth');

    Route::get('/show/{post}', 'PostController@show')
        ->name('show_post');

    Route::get('/create', 'PostController@create')
        ->name('create_post')
        ->middleware('can:create-post');

    Route::post('/create', 'PostController@store')
        ->name('store_post')
        ->middleware('can:create-post');

    Route::get('/edit/{post}', 'PostController@edit')
        ->name('edit_post')
        ->middleware('can:update-post,post');

    Route::post('/edit/{post}', 'PostController@update')
        ->name('update_post')
        ->middleware('can:update-post,post');

    Route::post('/delete/{post}', 'PostController@destory')
        ->name('delete_post')
        ->middleware('can:delete-post,post');

    // using get to simplify
    Route::get('/publish/{post}', 'PostController@publish')
        ->name('publish_post')
        ->middleware('can:publish-post,post');

    Route::get('/unpublish/{post}', 'PostController@unpublish')
        ->name('unpublish_post')
        ->middleware('can:publish-post,post');
});