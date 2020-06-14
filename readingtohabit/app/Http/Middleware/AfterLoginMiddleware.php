<?php

namespace App\Http\Middleware;

use App\User;
use App\AutoLoginToken;

use Closure;

class AfterLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (empty($request->session()->get('user_id'))) {
            // 自動ログイン用トークン(クッキー)が存在しないどうかを判定する
            if (empty($request->cookie('auto_login'))) {
                return redirect()->secure('login');
            }
            
            if (AutoLoginToken::check_validity_of_token($request->cookie('auto_login')) === false) {
                return redirect()->secure('login');
            }

            $current_token = AutoLoginToken::where('token', $request->cookie('auto_login'))->first();
            $auth_user     = User::where('id', $current_token['user_id'])->first();

            // 現在の自動ログイン用トークンを削除論理削除し、新たなトークンを作成する
            AutoLoginToken::soft_delete($current_token['id']);
            $new_token_info = AutoLoginToken::create_new_token($auth_user['id']);

            // 認証(セッション発行)
            $request->session()->put('user_id', $auth_user['id']);
            $request->session()->put('profile_img', $auth_user['profile_img']);
            $request->session()->put('current_date', date("YmdHis"));

            $response = $next($request);

            $response->cookie('auto_login', $new_token_info['token'], $new_token_info['expires_seconds'], true);
        }
        else {
            if (User::check_existense_of_user_info($request->session()->get('user_id')) === 'not_exists') {
                $request->session()->flush();
                
                return redirect()->secure('top');
            }

            $request->session()->regenerate();

            $response = $next($request);
        }

        return $response;
    }
}
