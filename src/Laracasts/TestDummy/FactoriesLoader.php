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
    private function normalizeDefinitions($designer)
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

            // Finally, we'll trigger Faker on the user-provided attributes.

            $definition->attributes = $this->triggerFakerOnAttributes($definition->attributes);

            return $definition;

        }, $designer->definitions());
    }

    /**
     * Apply Faker dummy values to the attributes.
     *
     * @param  array $attributes
     * @return array
     */
    private function triggerFakerOnAttributes(array $attributes)
    {
        // To ensure that we don't use the same Faker value for every
        // single factory of the same name, all Faker properties are
        // wrapped in closures.

        // So we can now filter through our attributes and call these
        // closures, which will generate the proper Faker values.

        return array_map(function ($attribute) {
            $attribute = is_callable($attribute) ? $attribute() : $attribute;

            // It's possible that the called Faker method returned an array.
            // If that is the case, we'll implode it for the user.

            return is_array($attribute) ? implode(' ', $attribute) : $attribute;
        }, $attributes);
    }

}
