<?php

namespace App\Http\Controllers;

use App\User;
use App\ResetPasswordToken;

use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordMailRequest;
use App\Http\Requests\ResetPasswordRequest;

use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use App\Mail\ResetPasswordFinish;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function reset_password_mail_do (ResetPasswordMailRequest $request) {
        // Readingtohabitに未登録のメールアドレスの際は、何もせずパスワードリセット用メールの送信完了画面へ遷移
        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return view('reset_password_mail.finish');
        }

        // パスワードリセット用トークンの生成
        do {
            $token = ResetPasswordToken::create_token($user['id']);
        } while ($token == 'duplicate_error');

        // パスワードリセット用メールの送信
        Mail::to($user['email'])->send(new ResetPassword($token));

        return view('reset_password_mail.finish');
    }

    public function reset_password_form (Request $request) {
        if (empty($request->input('key'))) {
            return view('common.invalid');
        }

        $token = ResetPasswordToken::where('token', $request->input('key'))->first();

        if (empty($token)) {
            return view('common.invalid');
        }

        if ($token['expires'] < Carbon::now()) {
            ResetPasswordToken::soft_delete($token['id']);

            return view('common.invalid');
        }

        $user = User::where('id', $token['user_id'])->first();
        if (empty($user)) {
            return view('common.invalid');
        }

        return view('reset_password.form',['token' => $token['token']]);
    }

    public function reset_password_do (ResetPasswordRequest $request) {
        if (empty($request->input('key'))) {
            return view('common.invalid');
        }
        
        $token = ResetPasswordToken::where('token', $request->input('key'))->first();

        if (empty($token)) {
            return view('common.invalid');
        }
        
        if ($token['expires'] < Carbon::now()) {
            ResetPasswordToken::soft_delete($token['id']);

            return view('common.invalid');
        }
        
        $user = User::where('id', $token['user_id'])->first();
        if (empty($user)) {
            return view('common.invalid');
        }

        User::where('id', $user['id'])->update(['password' => Hash::make($request->password)]);

        ResetPasswordToken::soft_delete($token['id']);

        Mail::to($user['email'])->send(new ResetPasswordFinish($user['name']));

        return view('reset_password.finish');
    }
}
