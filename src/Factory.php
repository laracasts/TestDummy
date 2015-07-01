<?php

namespace Laracasts\TestDummy;

use Laracasts\TestDummy\PersistableModel\IsPersistable;
use Laracasts\TestDummy\PersistableModel\EloquentModel;

class Factory
{

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
     * The persistable instance.
     *
     * @var IsPersistable
     */
    public static $databaseProvider;

    protected $builder;

    /**
     * Create a new factory instance.
     *
     * @param string        $factoriesPath
     * @param IsPersistable $databaseProvider
     */
    public function __construct($factoriesPath = null, IsPersistable $databaseProvider = null, Builder $builder = null)
    {
        $this->loadFactories($factoriesPath);
        $this->setDatabaseProvider($databaseProvider);
        $this->setBuilder($builder);
    }

    /**
     * Get the user-registered factories.
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
     * @return IsPersistable
     */
    public function databaseProvider()
    {
        return static::$databaseProvider;
    }

    /**
     * Build an array of dummy attributes for an entity.
     *
     * @param string $name
     * @param array  $attributes
     * @return array
     */
    public static function attributesFor($name, array $attributes = [])
    {
        return (new static)->getBuilder()->attributesFor($name, $attributes);
    }

    /**
     * Fill an entity with test data, without saving it.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return array
     */
    public static function build($name, array $attributes = [])
    {
        return (new static)->getBuilder()->build($name, $attributes);
    }

    /**
     * Fill and save an entity.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return mixed
     */
    public static function create($name, array $attributes = [])
    {
        return (new static)->getBuilder()->create($name, $attributes);
    }

    /**
     * Set the number of times to create a record.
     *
     * @param  integer $times
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
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Load the user provided factories.
     *
     * @param  string $factoriesPath
     * @return void
     */
    private function loadFactories($factoriesPath)
    {
        $factoriesPath = $factoriesPath ?: static::$factoriesPath;

        if ( ! static::$factories) {
            static::$factories = (new FactoriesLoader)->load($factoriesPath);
        }
    }

    /**
     * Set the database provider for the data generation.
     *
     * @param  IsPersistable $provider
     * @return void
     */
    private function setDatabaseProvider($provider)
    {
        if ( ! static::$databaseProvider) {
            static::$databaseProvider = $provider ?: new EloquentModel;
        }
    }

    protected function setBuilder(Builder $builder = null)
    {
        if (null == $builder) {
            $builder = new Builder($this->databaseProvider(), $this->factories());
        }

        $this->builder = $builder;
    }

}
