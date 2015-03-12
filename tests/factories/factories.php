<?php

$factory('User', 'user_with_role', [
    'name' => $faker->name,
    'email' => $faker->email,
    'password' => $faker->word,
    'role_id' => 'factory:role',
    'created_at' => $faker->date(),
    'updated_at' => $faker->date()
]);

$factory('Role', 'role', [
    'name' => $faker->name,
    'display_name' => $faker->name,
    'description' => $faker->sentence(),
    'created_at' => $faker->date(),
    'updated_at' => $faker->date()
]);
