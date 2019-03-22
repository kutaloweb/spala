<?php

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

$factory->define(App\Profile::class, function (Faker $faker) {
    $gender = $faker->randomElement(['male', 'female']);
    return [
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        'first_name' => $faker->firstName($gender),
        'last_name' => $faker->lastName,
        'gender' => $gender,
        'avatar' => $faker->imageUrl(),
        'phone' => null,
        'date_of_birth' => $faker->dateTimeThisCentury->format('Y-m-d'),
        'address_line_1' => null,
        'address_line_2' => null,
        'city' => null,
        'state' => null,
        'zipcode' => null,
        'country_id' => null,
    ];
});
