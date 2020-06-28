<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ArticleMailTimingSelectMaster;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\ArticleMailTiming;

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

$factory->define(ArticleMailTimingSelectMaster::class, function (Faker $faker) {
    return [
        'article_mail_timing_id' => 1,
    ];
});
