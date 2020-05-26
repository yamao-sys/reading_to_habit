<?php

namespace App\Http\Controllers;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;
use App\AutoLoginToken;
use App\ResetPasswordToken;
use App\Article;
use App\ArticleMailTiming;
use App\ArticleMailTimingMaster;
use App\ArticleMailTimingSelectMaster;

use Illuminate\Http\Request;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\EditPasswordRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class UserController extends Controller
{
    public function edit_profile_form (Request $request) {
        if (User::check_existense_of_user_info($request) === 'not_exists') {
            return view('common.invalid');
        }

        $user = User::where('id', $request->session()->get('user_id'))->first();
        
        $profile = [
                    'profile_img' => $user['profile_img'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                   ];

        return view('edit_user.profile', ['profile' => $profile]);
    }

    public function edit_profile_do (EditProfileRequest $request) {
        if (User::check_existense_of_user_info($request) === 'not_exists') {
            return view('common.invalid');
        }

        if (empty($request->profile_img)) {
            $profile_after_edit = User::edit_profile_excluding_img($request);
            if (empty($profile_after_edit)) {
                return view('common.fail');
            }
        }
        else {
            $profile_after_edit = User::edit_profile_including_img($request);
            if (empty($profile_after_edit)) {
                return view('common.fail');
            }

            $request->session()->put('profile_img', $profile_after_edit['profile_img']);
        }

        return view('edit_user.profile', ['profile' => $profile_after_edit, 'dialog' => 'プロフィールを更新しました。']);
    }

    public function edit_password_do (EditPasswordRequest $request) {
        if (User::check_existense_of_user_info($request) === 'not_exists') {
            return view('common.invalid');
        }

        if (User::edit_password($request) === false) {
            return view('common.fail');
        }

        return view('edit_user.password', ['dialog' => 'パスワードを更新しました。']);
    }

    public function edit_default_mail_timing_form (Request $request) {
        if (User::check_existense_of_user_info($request) === 'not_exists') {
            return view('common.invalid');
        }
        
        $def_timing = DefaultMailTiming::where('user_id', $request->session()->get('user_id'))->first();
        $def_timing_master = DefaultMailTimingMaster::where('default_mail_timing_id', $def_timing['id'])->first();
        $def_timing_select = DefaultMailTimingSelectMaster::where('default_mail_timing_id', $def_timing['id'])->first();

        $default_data = [
                         'default_mail_timing'        => $def_timing_master,
                         'default_mail_timing_select' => $def_timing_select,
                        ];

        return view('edit_user.default_mail_timing', $default_data);
    }

    public function edit_default_mail_timing_do (Request $request) {
        if (User::check_existense_of_user_info($request) === 'not_exists') {
            return view('common.invalid');
        }

        $def_timing_info_after_edit = User::edit_default_mail_timing($request);
        if (empty($def_timing_info_after_edit['default_mail_timing'])) {
            return view('common.fail');
        }
        
        $response_data = [
                            'dialog' => 'デフォルト配信タイミングを更新しました。',
                            'default_mail_timing'        => $def_timing_info_after_edit['default_mail_timing'],
                            'default_mail_timing_select' => $def_timing_info_after_edit['default_mail_timing_select'],
                         ];

        return view('edit_user.default_mail_timing', $response_data);
    }

    public function delete_user_do (Request $request) {
        if (User::check_existense_of_user_info($request) === 'not_exists') {
            return json_encode(['is_success' => false]);
        }

        if (User::soft_delete_user() && User::soft_delete_articles()) {
            $request->session()->flush();

            return json_encode(['is_success' => true]);
        }
        else {
            return json_encode(['is_success' => false]);
        }
    }
}
