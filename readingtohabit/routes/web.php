<?php

use Illuminate\Support\Facades\Route;

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

Route::get('top', function () {return view('top');});

Route::group(['middleware' => ['before_login']], function () {
    Route::get('/', 'AuthController@check_auto_login');
    
    // ユーザー登録関連
    Route::get('register_user_form', 'RegisterUserController@register_user_form');

    Route::post('register_user_check', 'RegisterUserController@register_user_check');
    Route::get('register_user_check', 'RegisterUserController@register_user_check_get');

    Route::get('register_user_do', function() {return view('common.invalid');});
    Route::post('register_user_do', 'RegisterUserController@register_user_do');

    Route::get('resend_mail_form', function () {return view('resend_mail.form');});

    Route::get('resend_mail_do', function() {return view('common.invalid');});
    Route::post('resend_mail_do', 'RegisterUserController@resend_mail_do');

    // ログイン関連
    Route::get('login', 'AuthController@check_auto_login');
    Route::post('login', 'AuthController@login_do');

    // パスワードリセット関連
    Route::get('reset_password_mail_form', function () {return view('reset_password_mail.form');});

    Route::get('reset_password_mail_do', function() {return view('common.invalid');});
    Route::post('reset_password_mail_do', 'ResetPasswordController@reset_password_mail_do');

    Route::get('reset_password_form', 'ResetPasswordController@reset_password_form');

    Route::get('reset_password_do', function() {return view('common.invalid');});
    Route::post('reset_password_do', 'ResetPasswordController@reset_password_do');

    // 退会後関連
    Route::get('delete_user_finish', function () {return view('delete_user.finish');});
});

Route::group(['middleware' => ['after_login']], function () {
    // 読書記録の投稿関連
    Route::get('add_article_search_book', function () {return view('article.add_article.search_book');});
    Route::get('add_article_form', 'ArticleController@add_article_form');
    Route::post('add_article_do', 'ArticleController@add_article_do');

    // 読書記録の閲覧関連
    Route::get('show_article/{article_id}', 'ArticleController@show_article');

    // お気に入り関連
    Route::post('add_favorite/{article_id}', 'ArticleController@add_favorite');
    Route::post('delete_favorite/{article_id}', 'ArticleController@delete_favorite');

    // 読書記録一覧関連
    Route::get('articles', 'ArticleController@articles');
    Route::get('favorites', 'ArticleController@favorites');

    // 読書記録の検索関連
    Route::get('search_article_form', function () {return view('article.search_article.form');});
    Route::post('search_results', 'ArticleController@search_results');
    Route::get('search_results', 'ArticleController@search_results');

    // 読書記録の更新関連
    Route::get('edit_article_form/{article_id}', 'ArticleController@edit_article_form');
    Route::post('edit_article_do/{article_id}', 'ArticleController@edit_article_do');

    // 読書記録の削除関連
    Route::post('delete_article_do/{article_id}', 'ArticleController@delete_article_do');

    // ユーザー編集関連
    Route::get('edit_profile', 'UserController@edit_profile_form');
    Route::post('edit_profile', 'UserController@edit_profile_do');
    Route::get('edit_password', 'UserController@edit_password_form');
    Route::post('edit_password', 'UserController@edit_password_do');
    Route::get('edit_default_mail_timing', 'UserController@edit_default_mail_timing_form');
    Route::post('edit_default_mail_timing', 'UserController@edit_default_mail_timing_do');

    // ユーザー削除関連
    Route::post('delete_user', 'UserController@delete_user_do');

    // ログアウト関連
    Route::get('logout', 'AuthController@logout');
});
