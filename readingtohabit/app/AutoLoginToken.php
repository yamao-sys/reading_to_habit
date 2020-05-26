<?php

namespace App;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AutoLoginToken extends Model
{
    protected $guarded = array('id');
    
    public function user () {
        return $this->belongsTo('App\User');
    }
    
    protected static function boot () {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
    }

    public static function soft_delete($id) {
        AutoLoginToken::where('id', $id)
                      ->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
    }

    public static function check_validity_of_token ($auto_login_token) {
        // 自動ログイン用トークン(クッキー)に相当するレコードの存在確認をする
        $current_token = AutoLoginToken::where('token', $auto_login_token)
                                       ->first();
        if (empty($current_token)) {
            return false;
        }

        // 自動ログイン用トークンの有効期限を確認する
        if ($current_token['expires'] <= Carbon::now()) {
            AutoLoginToken::soft_delete($current_token['id']);
            
            return false;
        }

        $user = User::where('id', $current_token['user_id'])->first();
        if (empty($user)) {
            AutoLoginToken::soft_delete($current_token['id']);
            
            return false;
        }
        
        return true;
    }

    public static function create_new_token ($user_id) {
        do {
            $new_token_info = AutoLoginToken::try_to_create_token($user_id);
        } while($new_token_info['token'] === 'duplicate_error');
        
        return $new_token_info;
    }
    
    public static function try_to_create_token ($user_id) {
        $new_token = str_random(255);
        
        $new_expires_seconds = time() + \AutoLoginTokenConst::EXPIRES_SECONDS;

        $new_token_info = [
                            'user_id' => $user_id,
                            'token'   => $new_token,
                            'expires' => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
                          ];
        
        try{
            $new_token_record = AutoLoginToken::create($new_token_info);
        }
        catch(IlluminateDatabaseQueryException $e) {
            $error_code = $e->errorInfo[1];

            if ($error_code == \ErrorCodeConst::DUPLICATE_ENTRY) {
                return [
                        'token' => 'duplicate_error',
                        'expires_seconds' => 0,
                       ];
            }
        }
        
        return [
                'token' => $new_token,
                'expires_seconds' => $new_expires_seconds,
               ];
    }
    
    public static function fetch_current_token($cookie) {
        // 自動ログイン用トークン(クッキー)に相当するレコードの存在確認をする
        $current_token = AutoLoginToken::where('token', $cookie)
                                       ->first();
        if (empty($current_token)) {
            return [];
        }

        // 自動ログイン用トークンの有効期限を確認する
        if ($current_token['expires'] <= Carbon::now()) {
            AutoLoginToken::soft_delete($current_token['id']);
            
            return [];
        }

        return $current_token;
    }
}
