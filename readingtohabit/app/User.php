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

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

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

    public static function edit_profile_including_img (Request $request) {
        $profile_img_name = 'profile_img_'.$request->session()->get('user_id').'.jpg';
        
        $request->profile_img->storeAs('img', $profile_img_name, 'public_uploads');
        
        $profile_img_path = \ImgPathConst::IMG_PATH.$profile_img_name;
        
        DB::beginTransaction();
        try {
            User::where('id', $request->session()->get('user_id'))
                ->update([
                          'name'  => $request->name,
                          'email' => $request->email,
                          'profile_img' => $profile_img_path,
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
    
    public static function soft_delete_user() {
        // 同時に削除する情報(=対象ユーザーに関連する情報)を取得
        $default_mail_timing = DefaultMailTiming::where('user_id', session()->get('user_id'))
                                                ->first();
        if (empty($default_mail_timing)) {
            return false;
        }
        $default_mail_timing_id = $default_mail_timing['id'];

        // 削除
        $delete_info = ['deleted' => 1, 'deleted_at' => Carbon::now()];
        DB::beginTransaction();
        
        try {
            User::where('id', session()->get('user_id'))->update($delete_info);
            AutoLoginToken::where('user_id', session()->get('user_id'))->update($delete_info);
            ResetPasswordToken::where('user_id', session()->get('user_id'))->update($delete_info);
            DefaultMailTiming::where('user_id', session()->get('user_id'))->update($delete_info);
            DefaultMailTimingMaster::where('default_mail_timing_id', $default_mail_timing_id)->update($delete_info);
            DefaultMailTimingSelectMaster::where('default_mail_timing_id', $default_mail_timing_id)->update($delete_info);
        }
        catch (Exception $e) {
            DB::rollback();
            return false;
        }
        
        DB::commit();

        return true;
    }
    
    public static function soft_delete_articles () {
        if (Article::count() === 0) {
            return true;
        }
        
        $articles = Article::get();

        foreach ($articles as $article) {
            $article_id[] = $article['id'];
        }

        $article_mail_timings = ArticleMailTiming::whereIn('article_id', $article_id)->get();
        foreach ($article_mail_timings as $article_mail_timing) {
            $article_mail_timing_id[] = $article_mail_timing['id'];
        }
            
        $delete_info = ['deleted' => 1, 'deleted_at' => Carbon::now()];
        DB::beginTransaction();
            
        try {
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
