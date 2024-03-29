<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ResetPasswordToken extends Model
{
    protected $guarded = array('id');

    protected static function boot() {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
    }

    public function user () {
        return $this->belongsTo('App\User');
    }
    
    public static function soft_delete($id) {
        ResetPasswordToken::where('id', $id)
                          ->update(['deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
    }

    public static function create_token ($user) {
        DB::beginTransaction();
        try {
            do {
                $token = ResetPasswordToken::try_to_create_token($user['id']);
            } while ($token === 'duplicate_error');
            
            Mail::to($user['email'])->send(new ResetPassword($token));
        }
        catch (Exception $e) {
            DB::rollback();
            return [];
        }
        DB::commit();

        return $token;
    }

    public static function try_to_create_token ($user_id) {
        $token = str_random(50);

        try {
            ResetPasswordToken::create([
                'user_id' => $user_id,
                'token'   => $token,
                'expires' => Carbon::now()->addHours(\ResetPasswordTokenConst::EXPIRES_HOURS),
            ]);
        }
        catch (IlluminateDatabaseQueryException $e) {
            $error_code = $e->errorInfo[1];

            if ($error_code === \ErrorCodeConst::DUPLICATE_ENTRY) {
                return 'duplicate_error';
            }
        }

        return $token;
    }

    public static function check_validity_of_token ($key) {
        if (empty($key)) {
            return false;
        }

        $token = ResetPasswordToken::where('token', $key)->first();

        if (empty($token)) {
            return false;
        }

        if ($token['expires'] < Carbon::now()) {
            ResetPasswordToken::soft_delete($token['id']);

            return false;
        }

        $user = User::where('id', $token['user_id'])->first();
        if (empty($user)) {
            return false;
        }

        return true;
    }
    
    public static function delete_expired_tokens () {
        $tokens = ResetPasswordToken::get();

        foreach ($tokens as $token) {
            if ($token->expires < Carbon::now()) {
                ResetPasswordToken::soft_delete($token->id);
            }
        }

        return;
    }
}
