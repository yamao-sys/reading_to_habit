<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DefaultMailTiming extends Model
{
    // protected $guarded = array('id');
    protected $guarded = array('id');
    
    public function default_mail_timing_master () {
        return $this->hasOne('App\DefaultMailTimingMaster');
    }
    
    public function default_mail_timing_select_master () {
        return $this->hasOne('App\DefaultMailTimingSelectMaster');
    }
    
    protected static function boot () {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
    }
}
