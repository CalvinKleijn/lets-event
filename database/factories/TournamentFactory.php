<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Tournament;
use Faker\Generator as Faker;

$factory->define(Tournament::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence,
        'description' => $faker->sentence,
        'start_date_time' => $faker->dateTime()
    ];
});
