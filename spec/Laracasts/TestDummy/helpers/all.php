<?php

$factory('Album', [
    'name'   => $faker->word,
    'artist' => $faker->word
]);

$factory('Artist', function ($faker) {
    return [
        'name' => $faker->name
    ];
});

$factory('Foo', function ($faker) {
    return [
        'name' => $faker->name
    ];
});
