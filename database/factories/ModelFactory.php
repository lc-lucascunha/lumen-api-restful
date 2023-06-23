<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Client;
use App\Models\Product;
use Faker\Generator as Faker;

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

$factory->define(Client::class, function (Faker $faker) {
    return [
        'name'       => $faker->name,
        'email'      => $faker->unique()->safeEmail,
        'phone'      => $faker->regexify('\d{11}'),
        'birthdate'  => $faker->date('Y-m-d'),
        'zip_code'   => $faker->regexify('\d{8}'),
        'address'    => $faker->streetAddress,
        'province'   => $faker->city,
        'complement' => $faker->optional()->secondaryAddress,
    ];
});

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name'  => $faker->unique()->word,
        'price' => $faker->randomFloat(2, 9, 999),
        'photo' => $faker->imageUrl(),
    ];
});
