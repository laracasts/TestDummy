<?php

/**
 * This function has the same name as the fixture `scheduled_post` and MUST NOT be called
 * Fixtures with short name should be allowed to use the name of an existing function
 *
 * @throws Exception
 */
function scheduled_post()
{
    throw new \Exception(
        'Function `scheduled_post()` MUST NOT be called. ' .
        'Fixture with a short name of an existing function should be allowed.'
    );
}

$factory('Post', 'scheduled_post', [
    'title' => 'Scheduled Post Title'
]);

$factory('Post', [
    'title' => 'Post Title'
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