<?php

namespace App;

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

use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessRegisterUser;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Storage;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     /*
    protected $fillable = [
        'name', 'email', 'password'
    ];
    */
    
    /*
    protected $fillable = [
        'name', 'email', 'password', 'deleted', 'deleted_at'
    ];
    */

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    protected $guarded = array('id');

    public static $rules = array(
        'name' => 'required|regex:/\A[0-9a-zA-Z]{1,20}\z/',
        'email' => 'required|unique:users,email',
        'password' => 'required|regex:/\A[0-9a-zA-Z]{6,12}\z/',
    );

    protected static function boot () {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
    }

    public function default_mail_timing () {
        return $this->hasOne('App\DefaultMailTiming');
    }

    public function auto_login_token () {
        return $this->hasMany('App\AutoLoginToken');
    }
    
    public function reset_password_token () {
        return $this->hasMany('App\ResetPasswordToken');
    }

    public function article () {
        return $this->hasMany('App\Article');
    }

    public static function create_user ($register_user_info) {
        DB::beginTransaction();

        try {
            $user = User::create($register_user_info);
            $default_mail_timing = $user->default_mail_timing()->create();
            $default_mail_timing->default_mail_timing_master()->create();
            $default_mail_timing->default_mail_timing_select_master()->create();
            Mail::to($register_user_info['email'])->send(new SuccessRegisterUser($register_user_info['name'],$register_user_info['email']));
        }
        catch (Exception $e) {
            DB::rollback();
            return false;
        }

        DB::commit();
        
        return true;
    }
    
    public static function make_default_mail_timing_select_info (Request $request) {
        if ($request->mail_timing_select === 'by_day') {
            return  [
                     'by_day'   => 1,
                     'by_week'  => 0,
                     'by_month' => 0,
                    ];
        }
        elseif ($request->mail_timing_select === 'by_week') {
            return [
                    'by_day'   => 0,
                    'by_week'  => 1,
                    'by_month' => 0,
                   ];
        }
        elseif ($request->mail_timing_select === 'by_month') {
            return [
                    'by_day'   => 0,
                    'by_week'  => 0,
                    'by_month' => 1,
                   ];
        }
        else {
            return [
                    'by_day'   => 1,
                    'by_week'  => 0,
                    'by_month' => 0,
                   ];
        }
    }

    public static function make_default_mail_timing_info (Request $request) {
        return [
                'by_day'   => $request->mail_timing_by_day,
                'by_week'  => $request->mail_timing_by_week,
                'by_month' => $request->mail_timing_by_month,
               ];
    }
    
    public static function check_existense_of_user_info ($user_id) {
        $user = User::where('id', $user_id)->first();
        if (empty($user)) {
            return 'not_exists';
        }

        $def_timing = DefaultMailTiming::where('user_id', $user['id'])->first();
        if (empty($def_timing)) {
            return 'not_exists';
        }
        
        $def_timing_select = DefaultMailTimingSelectMaster::where('default_mail_timing_id', $def_timing['id'])
                                                          ->first();
        if (empty($def_timing_select)) {
            return 'not_exists';
        }
        
        $def_timing_master = DefaultMailTimingMaster::where('default_mail_timing_id', $def_timing['id'])
                                                    ->first();
        if (empty($def_timing_master)) {
            return 'not_exists';
        }
        
        return 'exists';
    }

    public static function edit_profile_including_img (Request $request) {
        $path = Storage::disk('s3')->putFile('profile_img', $request->file('profile_img'), 'public');
        
        DB::beginTransaction();
        try {
            User::where('id', $request->session()->get('user_id'))
                ->update([
                          'name'  => $request->name,
                          'email' => $request->email,
                          'profile_img' => Storage::disk('s3')->url($path),
                          'updated_at'  => Carbon::now(),
                         ]);
        }
        catch (Exception $e) {
            DB::rollback();
            return [];
        }
        DB::commit();

        return User::where('id', $request->session()->get('user_id'))->first();
    }

    public static function edit_profile_excluding_img(Request $request) {
        DB::beginTransaction();
        try {
            User::where('id', $request->session()->get('user_id'))
                ->update([
                          'name'  => $request->name,
                          'email' => $request->email,
                          'updated_at'  => Carbon::now(),
                         ]);
        }
        catch (Exception $e) {
            DB::rollback();
            return [];
        }
        DB::commit();

        return User::where('id', $request->session()->get('user_id'))->first();
    }

    public static function edit_password (Request $request) {
        DB::beginTransaction();
        try {
            User::where('id', $request->session()->get('user_id'))
                ->update([
                          'password'   => Hash::make($request->new_password),
                          'updated_at' => Carbon::now(),
                         ]);
        }
        catch (Exception $e) {
            DB::rollback();
            return false;
        }
        DB::commit();

        return true;
    }


    public static function edit_default_mail_timing (Request $request) {
        $def_timing = DefaultMailTiming::where('user_id', $request->session()->get('user_id'))->first();
        $edit_def_timing_info        = User::make_default_mail_timing_info($request);
        $edit_def_timing_select_info = User::make_default_mail_timing_select_info($request);
        
        DB::beginTransaction();
        try {
            DefaultMailTimingMaster::where('default_mail_timing_id', $def_timing['id'])
                                   ->update($edit_def_timing_info);
            
            DefaultMailTimingSelectMaster::where('default_mail_timing_id', $def_timing['id'])
                                         ->update($edit_def_timing_select_info);
        }
        catch (Exception $e) {
            DB::rollback();
            return ['default_mail_timing' => '', 'defautl_mail_timing_select' => ''];
        }
        DB::commit();

        $info['default_mail_timing']        = DefaultMailTimingMaster::where('default_mail_timing_id', $def_timing['id'])->first();
        $info['default_mail_timing_select'] = DefaultMailTimingSelectMaster::where('default_mail_timing_id', $def_timing['id'])->first();

        return $info;
    }

    public static function soft_delete_user() {
        // 同時に削除する情報(=対象ユーザーに関連する情報)を取得
        $default_mail_timing = DefaultMailTiming::where('user_id', session()->get('user_id'))
                                                ->first();
        if (empty($default_mail_timing)) {
            return false;
        }
        
        $articles = Article::get();

        foreach ($articles as $article) {
            $article_id[] = $article['id'];
        }

        $article_mail_timings = ArticleMailTiming::whereIn('article_id', $article_id)->get();
        foreach ($article_mail_timings as $article_mail_timing) {
            $article_mail_timing_id[] = $article_mail_timing['id'];
        }

        // 削除
        $delete_info = ['deleted' => 1, 'deleted_at' => Carbon::now()];
        DB::beginTransaction();
        try {
            User::where('id', session()->get('user_id'))->update($delete_info);
            AutoLoginToken::where('user_id', session()->get('user_id'))->update($delete_info);
            ResetPasswordToken::where('user_id', session()->get('user_id'))->update($delete_info);
            DefaultMailTiming::where('user_id', session()->get('user_id'))->update($delete_info);
            DefaultMailTimingMaster::where('default_mail_timing_id', $default_mail_timing['id'])->update($delete_info);
            DefaultMailTimingSelectMaster::where('default_mail_timing_id', $default_mail_timing['id'])->update($delete_info);
            Article::where('user_id', session()->get('user_id'))->update($delete_info);
            ArticleMailTiming::whereIn('article_id', $article_id)->update($delete_info);
            ArticleMailTimingMaster::whereIn('article_mail_timing_id', $article_mail_timing_id)->update($delete_info);
            ArticleMailTimingSelectMaster::whereIn('article_mail_timing_id', $article_mail_timing_id)->update($delete_info);
        }
        catch (Exception $e) {
            DB::rollback();
            return false;
        }
        DB::commit();

        return true;
    }
}
