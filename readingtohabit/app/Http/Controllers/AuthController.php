<?php

namespace App\Http\Controllers;

use App\User;
use App\AutoLoginToken;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function check_auto_login(Request $request) {
        if (empty($request->cookie('auto_login'))) {
            return view('auth.login');
        }

        if (AutoLoginToken::check_validity_of_token($request->cookie('auto_login')) === false) {
            return view('auth.login');
        }

        $current_token = AutoLoginToken::where('token', $request->cookie('auto_login'))->first();
        $auth_user     = User::where('id', $current_token['user_id'])->first();

        // 現在の自動ログイン用トークンを削除論理削除し、新たなトークンを作成する
        AutoLoginToken::soft_delete($current_token['id']);
        $new_token_info = AutoLoginToken::create_new_token($auth_user['id']);

        // 認証(セッション発行)
        $request->session()->put('user_id', $auth_user['id']);
        $request->session()->put('profile_img', $auth_user['profile_img']);

        return redirect('articles')
               ->withCookie('auto_login', $new_token_info['token'], $new_token_info['expires_seconds'], true);
    }

    public function login_do (LoginRequest $request) {
        // 入力値のメールアドレス・パスワードに相当するユーザーの存在確認をする
        $auth_user = User::where('email', $request->email)->first();

        if (empty($auth_user) || password_verify($request->password, $auth_user['password']) === false) {
            unset($request->_token);

            return back()->withErrors(['is_not_exist' => '存在しないユーザーです。'])
                         ->withInput($request->except('password'));
        }

        // 認証(セッション発行)
        $request->session()->put('user_id', $auth_user['id']);
        $request->session()->put('profile_img', $auth_user['profile_img']);
        
        if (isset($request->auto_login)) {
            $new_token_info = AutoLoginToken::create_new_token($auth_user['id']);

            return redirect('articles')
                   ->withCookie('auto_login', $new_token_info['token'], $new_token_info['expires_seconds'], true);
        }

        return redirect('articles');
    }

    /*
    public function logout() {
        $request->session()->flush();

        return redirect('');
    }
    */
}
