<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DefaultMailTiming;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\User;

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

$factory->define(DefaultMailTiming::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
