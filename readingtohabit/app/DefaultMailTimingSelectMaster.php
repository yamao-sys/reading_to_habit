<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DefaultMailTimingSelectMaster extends Model
{
    protected $guarded = array('id');
    
    protected static function boot () {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
    }
}
