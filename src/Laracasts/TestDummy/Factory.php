<?php namespace Laracasts\TestDummy;

use Laracasts\TestDummy\BuildableRepositoryInterface as BuildableRepository;

class Factory {

    /**
     * The path to the factories directory.
     *
     * @var string
     */
    public static $factoriesPath = 'tests/factories';

    /**
     * The user registered fatories.
     *
     * @var array
     */
    private $factories;

    /**
     * The persistence layer.
     *
     * @var BuildableRepositoryInterface
     */
    public static $databaseProvider;

    /**
     * Create a new factory instance.
     *
     * @param string $factoriesPath
     * @param BuildableRepository $databaseProvider
     */
    public function __construct($factoriesPath = null, BuildableRepository $databaseProvider = null)
    {
        if ($factoriesPath)
        {
            static::$factoriesPath = $factoriesPath;
        }

        static::$databaseProvider = $databaseProvider ?: new EloquentDatabaseProvider;

        $this->factories = $this->loadFactories();
    }

    /**
     * Get the user registered factories.
     *
     * @return array
     */
    public function factories()
    {
        return $this->factories;
    }

    /**
     * Get the database provider.
     *
     * @return BuildableRepositoryInterface
     */
    public function databaseProvider()
    {
        return static::$databaseProvider;
    }

    /**
     * Fill an entity with test data, without saving it.
     *
     * @param string $name
     * @param array  $attributes
     * @return array
     */
    public static function build($name, array $attributes = [])
    {
        return (new static)->getBuilder()->build($name, $attributes);
    }

    /**
     * Fill and save an entity.
     *
     * @param string $name
     * @param array  $attributes
     * @return mixed
     */
    public static function create($name, array $attributes = [])
    {
        return (new static)->getBuilder()->create($name, $attributes);
    }

    /**
     * Set the number of times to create a record.
     *
     * @param $times
     * @return $this
     */
    public static function times($times)
    {
        return (new static)->getBuilder()->setTimes($times);
    }

    /**
     * Create a Builder instance.
     *
     * @return Builder
     */
    private function getBuilder()
    {
        return new Builder($this->databaseProvider(), $this->factories());
    }

    /**
     * Load the factories.
     *
     * @return void
     */
    private function loadFactories()
    {
        $this->assertThatFactoriesDirectoryExists($basePath = static::$factoriesPath);

        $designer = new Designer;
        $faker = new FakerAdapter;

        $factory = function($name, $shortName, $attributes = []) use ($designer, $faker)
        {
            return $designer->define($name, $shortName, $attributes);
        };

        foreach ((new FactoriesFinder($basePath))->find() as $fixture)
        {
            include($fixture);
        }

        return $designer->definitions();
    }

    /**
     * Assert that the given factories directory exists.
     *
     * @param  string $basePath
     * @return mixed
     */
    private function assertThatFactoriesDirectoryExists($basePath)
    {
        if ( ! is_dir($basePath))
        {
            throw new TestDummyException(
                "The path provided for the factories, {$basePath}, directory does not exist."
            );
        }
    }

}