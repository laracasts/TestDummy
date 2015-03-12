<?php

$factory('Album', [
    'name'   => $faker->word,
    'artist' => $faker->word
]);

$factory('Artist', function ($faker, $attributes) {
    return [
        'name' => $faker->name,
    ];
});
