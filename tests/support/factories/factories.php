<?php

$factory('Post', [
    'title' => 'Post Title'
]);

$factory('Comment', [
    'post_id' => 'factory:Post',
    'body' => $faker->word
]);