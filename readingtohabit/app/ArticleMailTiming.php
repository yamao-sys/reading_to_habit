<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ArticleMailTiming extends Model
{
    protected $guarded = array('id');
    
    public function article_mail_timing_master () {
        return $this->hasOne('App\ArticleMailTimingMaster');
    }
    
    public function article_mail_timing_select_master () {
        return $this->hasOne('App\ArticleMailTimingSelectMaster');
    }
    
    protected static function boot () {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
    }
}
