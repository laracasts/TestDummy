<?php

namespace Laracasts\TestDummy;

use Faker\Factory as Faker;

class FactoriesLoader
{

    /**
     * Load the factories.
     *
     * @param  string $basePath
     * @return array
     */
    public function load($basePath)
    {
        $this->assertThatFactoriesDirectoryExists($basePath);

        $designer = new Designer;
        $faker = new FakerAdapter;

        $factory = function ($name, $shortName, $attributes = []) use ($designer, $faker) {
            return $designer->define($name, $shortName, $attributes);
        };

        foreach ((new FactoriesFinder($basePath))->find() as $file) {
            include($file);
        }

        return $this->normalizeDefinitions($designer);
    }

    /**
     * Assert that the given factories directory exists.
     *
     * @param  string $basePath
     * @return mixed
     * @throws TestDummyException
     */
    private function assertThatFactoriesDirectoryExists($basePath)
    {
        if ( ! is_dir($basePath)) {
            throw new TestDummyException(
                "The path provided for the factories directory, {$basePath}, does not exist."
            );
        }
    }

    /**
     * Normalize factory calls, so that the user may provide either
     * an array or closure to define the makeup of the entity.
     *
     * @param  Designer $designer
     * @return array
     */
    private function normalizeDefinitions(Designer $designer)
    {
        $faker = Faker::create();

        return array_map(function ($definition) use ($faker) {

            // If the user provided a closure, then we need to trigger
            // it, and fetch the returned array.

            if (is_callable($definition->attributes)) {
                $definition->attributes = call_user_func($definition->attributes, $faker);

                // And if the user didn't return an array from that closure, well
                // we don't exactly know how to proceed. So we'll abort.

                if ( ! is_array($definition->attributes)) {
                    throw new TestDummyException("Factory closure must return an array of attributes.");
                }
            }

            return $definition;
        }, $designer->definitions());
    }

}
