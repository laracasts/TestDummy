<?php

$factory('Post', [
    'title' => 'Post Title'
]);

$factory('Post', 'scheduled_post', [
    'title' => 'Scheduled Post Title'
]);

$factory('Comment', [
    'post_id' => 'factory:Post',
    'body' => $faker->word
]);

$factory('Foo', function($faker) {
    return [
        'name' => $faker->word
    ];
});