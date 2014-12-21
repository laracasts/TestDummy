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
     * The user registered factories.
     *
     * @var array
     */
    private static $factories;

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

        $this->loadFactories();

        static::$databaseProvider = $databaseProvider ?: new EloquentDatabaseProvider;
    }

    /**
     * Get the user registered factories.
     *
     * @return array
     */
    public function factories()
    {
        return static::$factories;
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
     * Load the user provided factories.
     *
     * @return void
     */
    private function loadFactories()
    {
        if ( ! static::$factories)
        {
            static::$factories = (new FactoriesLoader)->load(static::$factoriesPath);
        }
    }

}