<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AutoLoginToken;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(AutoLoginToken::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'token' => Str::random(255),
        'expires' => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
    ];
});
