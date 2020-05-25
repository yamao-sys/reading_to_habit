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
use App\Mail\SuccessRegisterUser;
use App\Mail\ResendMail;

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
        if (empty($request->session()->get('register_user_info_name'))) {
            return view('common.invalid');
        }
        
        $name     = $request->session()->get('register_user_info_name');
        $email    = $request->session()->get('register_user_info_email');
        $password = $request->session()->get('register_user_info_password');
        
        $password_to_print = '';
        for ($i = 0; $i < strlen($password); $i++) {
            $password_to_print .= '*';
        }
        $register_user_info = [
            'name'     => $name,
            'email'    => $email,
            'password' => $password_to_print,
        ];
        
        return view('register_user.check', ['register_user_info' => $register_user_info]);
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

        DB::beginTransaction();

        try {
            $user = new User($register_user_info);
            $user->save();
            $default_mail_timing = $user->default_mail_timing()->create();
            $default_mail_timing->default_mail_timing_master()->create();
            $default_mail_timing->default_mail_timing_select_master()->create();
        }
        catch (Exception $e) {
            DB::rollback();
            return back()->withInput();
        }

        DB::commit();

        Mail::to($email)->send(new SuccessRegisterUser($name,$email));

        $request->session()->flush();

        return view('register_user.finish');
    }

    public function resend_mail_form () {
        return view('resend_mail.form');
    }

    public function resend_mail_do (ResendMailRequest $request) {
        $user = User::where('email', $request->email)->first();

        Mail::to($user['email'])->send(new SuccessRegisterUser($user['name'], $user['email']));

        return view('resend_mail.finish');
    }
}
