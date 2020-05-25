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
        $user = User::where('id', $request->session()->get('user_id'))->first();

        if (empty($user)) {
            return view('common.invalid');
        }

        $profile = [
                    'profile_img' => $user['profile_img'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                   ];

        return view('edit_user.profile', ['profile' => $profile]);
    }

    public function edit_profile_do (EditProfileRequest $request) {
        $user = User::where('id', $request->session()->get('user_id'))->first();

        if (empty($user)) {
            return view('common.invalid');
        }

        if (empty($request->profile_img)) {
            $profile_after_edit = User::edit_profile_excluding_img($request);
        }
        else {
            $profile_after_edit = User::edit_profile_including_img($request);

            $request->session()->put('profile_img', $profile_after_edit['profile_img']);
        }

        return view('edit_user.profile', ['profile' => $profile_after_edit, 'dialog' => 'プロフィールを更新しました。']);
    }
    
    public function edit_password_form (Request $request) {
        return view('edit_user.password');
    }

    public function edit_password_do (EditPasswordRequest $request) {
        $user = User::where('id', $request->session()->get('user_id'))->first();
        
        if (empty($user)) {
            return view('common.invalid');
        }

        DB::beginTransaction();

        try {
            User::where('id', $request->session()->get('user_id'))
                ->update(['password' => Hash::make($request->new_password)]);

            AutoLoginToken::where('user_id', $request->session()->get('user_id'))
                          ->update(['deleted' => 1, 'deleted_at' => Carbon::now()]);
        }
        catch (Exception $e) {
            DB::rollback();
            return back()->withInput();
        }
        
        DB::commit();

        return view('edit_user.password', ['dialog' => 'パスワードを更新しました。']);
    }

    public function edit_default_mail_timing_form (Request $request) {
        $user = User::where('id', $request->session()->get('user_id'))->first();
        
        if (empty($user)) {
            return view('common.invalid');
        }

        $default_mail_timing = DefaultMailTiming::where('user_id', $user['id'])->first();
        if (empty($default_mail_timing)) {
            return view('common.invalid');
        }
        
        $default_mail_timing_select_master = DefaultMailTimingSelectMaster::where('default_mail_timing_id', $default_mail_timing['id'])
                                                                          ->first();
        if (empty($default_mail_timing_select_master)) {
            return view('common.invalid');
        }
        
        $default_mail_timing_master = DefaultMailTimingMaster::where('default_mail_timing_id', $default_mail_timing['id'])
                                                                          ->first();
        if (empty($default_mail_timing_master)) {
            return view('common.invalid');
        }

        $default_data = [
                            'default_mail_timing_select' => $default_mail_timing_select_master,
                            'default_mail_timing' => $default_mail_timing_master,
                        ];

        return view('edit_user.default_mail_timing', $default_data);
    }

    public function edit_default_mail_timing_do (Request $request) {
        $user = User::where('id', $request->session()->get('user_id'))->first();
        
        if (empty($user)) {
            return view('common.invalid');
        }

        $default_mail_timing = DefaultMailTiming::where('user_id', $user['id'])->first();

        $default_mail_timing_select_info = User::make_default_mail_timing_select_info($request);
        $default_mail_timing_info = User::make_default_mail_timing_info($request);
        
        DB::beginTransaction();

        try {
            DefaultMailTimingSelectMaster::where('default_mail_timing_id', $default_mail_timing['id'])
                                         ->update($default_mail_timing_select_info);

            DefaultMailTimingMaster::where('default_mail_timing_id', $default_mail_timing['id'])
                                   ->update($default_mail_timing_info);
        }
        catch (Exception $e) {
            DB::rollback();
            return back()->withInput();
        }

        DB::commit();

        $updated_default_mail_timing_select = DefaultMailTimingSelectMaster::where('default_mail_timing_id', $default_mail_timing['id'])
                                                                           ->first();
        $updated_default_mail_timing = DefaultMailTimingMaster::where('default_mail_timing_id', $default_mail_timing['id'])
                                                              ->first();

        $response_data = [
                            'default_mail_timing_select' => $updated_default_mail_timing_select,
                            'default_mail_timing' => $updated_default_mail_timing,
                            'dialog' => 'デフォルト配信タイミングを更新しました。',
                         ];

        return view('edit_user.default_mail_timing', $response_data);
    }

    public function delete_user_do (Request $request) {
        $user = User::where('id', $request->session()->get('user_id'))->first();
        
        if (empty($user)) {
            return json_encode(['is_success' => false]);
        }

        $is_success_delete = User::soft_delete_user() && User::soft_delete_articles();

        if ($is_success_delete) {
            $request->session()->flush();

            return json_encode(['is_success' => true]);
        }
        else {
            return json_encode(['is_success' => false]);
        }
    }
}
