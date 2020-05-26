<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AutoLoginToken;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

use Carbon\Carbon;

$factory->define(AutoLoginToken::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'token' => Str::random(255),
        'expires' => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
    ];
});
