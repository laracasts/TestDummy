<?php namespace Laracasts\TestDummy;

use Faker\Factory as Faker;

class Factory {

    /**
     * The path to the factory files.
     *
     * @var array
     */
    private static $factories;

    /**
     * The persistence layer.
     *
     * @var BuildableRepositoryInterface
     */
    private static $databaseProvider;

    /**
     * Fill an entity with test data, without saving it.
     *
     * @param string $type
     * @param array  $attributes
     * @return array
     */
    public static function build($type, array $attributes = [])
    {
        return static::instance()->build($type, $attributes);
    }

    /**
     * Fill and save an entity.
     *
     * @param string $type
     * @param array  $attributes
     * @return mixed
     */
    public static function create($type, array $attributes = [])
    {
        return static::instance()->create($type, $attributes);
    }

    /**
     * Set the number of times to create a record.
     *
     * @param $times
     * @return $this
     */
    public static function times($times)
    {
        return static::instance()->setTimes($times);
    }

    /**
     * Create a new Builder instance.
     *
     * @return Builder
     */
    private static function instance()
    {
        if ( ! static::$factories) static::setFactoriesPath();
        if ( ! static::$databaseProvider) static::setDatabaseProvider(new EloquentDatabaseProvider);

        return new Builder(static::$databaseProvider, static::$factories);
    }

    /**
     * Set the factories path.
     *
     * @param $basePath
     */
    public static function setFactoriesPath($basePath = null)
    {
        if ( ! $basePath)
        {
            $basePath = base_path('tests/factories');
        }

        if ( ! is_dir($basePath))
        {
            throw new TestDummyException('The path provided for the factories directory does not exist.');
        }

        static::loadFactories($basePath);
    }

    /**
     * Set the database provider.
     *
     * @param BuildableRepositoryInterface $provider
     */
    public static function setDatabaseProvider(BuildableRepositoryInterface $provider)
    {
        return static::$databaseProvider = $provider;
    }

    /**
     * Load the factories.
     *
     * @return void
     */
    public static function loadFactories($basePath)
    {
        $designer = new Designer;
        $faker = Faker::create();
        $finder = new FactoriesFinder($basePath);

        $factory = function($name, $shortName, $attributes = []) use ($designer, $faker)
        {
            return $designer->define($name, $shortName, $attributes);
        };

        foreach ($finder->find() as $fixture)
        {
            include($fixture);
        }

        return static::$factories = $designer->definitions();
    }

}