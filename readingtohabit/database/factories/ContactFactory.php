<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contact;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'learning' => 'abcde',
    ];
});
