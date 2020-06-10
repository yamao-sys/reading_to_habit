<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ArticleMailTiming;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Article;

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

$factory->define(ArticleMailTiming::class, function (Faker $faker) {
    return [
        'article_id' => function () {
            return factory(Article::class)->create()->id;
        },
    ];
});
