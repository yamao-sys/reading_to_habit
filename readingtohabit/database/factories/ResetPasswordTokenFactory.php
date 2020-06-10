<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ResetPasswordToken;
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

$factory->define(ResetPasswordToken::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'token' => Str::random(50),
        'expires' => Carbon::now()->addHours(\ResetPasswordTokenConst::EXPIRES_HOURS),
    ];
});
