<?php

namespace App\Http\Controllers;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResendMailRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResendMail;
use App\Mail\SuccessRegisterUser;

class registerUserController extends Controller
{
    public function register_user_form (Request $request) {
        // 登録情報確認画面から登録情報修正リンクがクリックされた時のため
        $request->session()->regenerate();
        
        return view('register_user.form');
    }

    public function register_user_check (RegisterUserRequest $request) {
        // 入力された情報をセッション変数に格納しておくことで、登録情報入力画面へ戻ったとき、情報を保持できる
        $request->session()->regenerate();
        $request->session()->put('register_user_info_name',     $request->name);
        $request->session()->put('register_user_info_email',    $request->email);
        $request->session()->put('register_user_info_password', $request->password);

        $password_to_print = '';
        for ($i=0; $i < strlen($request->password); $i++) {
            $password_to_print .= '*';
        }

        $user_info = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $password_to_print,
        ];

        return view('register_user.check', ['user_info' => $user_info]);
    }

    public function register_user_check_get (Request $request) {
        $request->session()->regenerate();
        
        // 登録情報入力画面を経ずに登録情報確認画面へアクセスされるのを防ぐため
        if (empty($request->session()->get('register_user_info_name'))) {
            return view('common.invalid');
        }
        
        $password_to_print = '';
        for ($i = 0; $i < strlen($request->session()->get('register_user_info_password')); $i++) {
            $password_to_print .= '*';
        }
        $user_info = [
            'name'     => $request->session()->get('register_user_info_name'),
            'email'    => $request->session()->get('register_user_info_email'),
            'password' => $password_to_print,
        ];
        
        return view('register_user.check', ['user_info' => $user_info]);
    }

    public function register_user_do (Request $request) {
        $request->session()->regenerate();
        if (empty($request->session()->get('register_user_info_name'))) {
            return view('common.invalid');
        }
        
        $name     = $request->session()->get('register_user_info_name');
        $email    = $request->session()->get('register_user_info_email');
        $password = $request->session()->get('register_user_info_password');

        $register_user_info = [
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ];

        if (User::create_user($register_user_info) === false) {
            $request->session()->flush();
            return view('common.fail');
        }

        $request->session()->flush();
        return view('register_user.finish');
    }

    public function resend_mail_do (ResendMailRequest $request) {
        $user = User::where('email', $request->email)->first();
        // 入力されたメールアドレスのユーザーが存在しなくても、送信完了画面を表示する
        if (empty($user)) {
            return view('resend_mail.finish');
        }

        Mail::to($user['email'])->send(new SuccessRegisterUser($user['name'], $user['email']));

        return view('resend_mail.finish');
    }
}
