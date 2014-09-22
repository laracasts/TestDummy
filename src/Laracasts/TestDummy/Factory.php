<?php namespace Laracasts\TestDummy;

use Faker\Factory as FakerFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * Convenience Laravel bootstrap for TestDummy
 * Factory::times(2)->create('Post')
 *
 * @package Laracasts\TestDummy
 */
class Factory {

	/**
	 * The path to the fixtures file
	 *
	 * @var array
	 */
	protected static $fixtures;

	/**
	 * Persistence layer
     *
     * @var BuildableRepositoryInterface
	 */
	protected static $databaseProvider;

    /**
     * Attribute replacer
     *
     * @var AttributeReplacer
     */
    protected static $attributeReplacer;

	/**
	 * Create a new Builder instance.
	 *
	 * @return Builder
	 */
	protected static function getInstance()
	{
		if ( ! static::$fixtures) static::setFixtures();
		if ( ! static::$databaseProvider) static::setDatabaseProvider();
        if ( ! static::$attributeReplacer) static::setAttributeReplacer();

		return new Builder(static::$databaseProvider, static::$attributeReplacer, static::$fixtures);
	}

	/**
	 * Fill an entity with test data, without saving it.
	 *
	 * @param string $type
	 * @param array  $attributes
	 * @return array
	 */
	public static function build($type, array $attributes = [])
	{
		return static::getInstance()->build($type, $attributes);
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
		return static::getInstance()->create($type, $attributes);
	}

	/**
	 * Set the number of times to create a record.
	 *
	 * @param $times
	 * @return $this
	 */
	public static function times($times)
	{
		return static::getInstance()->setTimes($times);
	}

	/**
	 * Set the fixtures path
	 *
	 * @param $basePath
	 */
	public static function setFixtures($basePath = null)
	{
		if ( ! $basePath)
		{
			$basePath = file_exists(base_path('tests')) ? base_path('tests') : app_path('tests');
		}

		$finder = new FixturesFinder($basePath);

		static::$fixtures = Yaml::parse($finder->find());
	}

	/**
	 * Set the database provider
	 *
	 * @param null $provider
	 * @return EloquentDatabaseProvider
	 */
	public static function setDatabaseProvider($provider = null)
	{
		$provider = $provider ?: new EloquentDatabaseProvider;

		return static::$databaseProvider = $provider;
	}

    /**
     * Set the attribute replacer
     *
     * @param null $attributeReplacer
     * @return AttributeReplacer
     */
    public static function setAttributeReplacer($attributeReplacer = null)
    {
        $attributeReplacer = $attributeReplacer ?: new DynamicAttributeReplacer();

        return static::$attributeReplacer = $attributeReplacer;
    }

    /**
     * Helper method to quickly set the faker attribute replacer.
     *
     * @param string $locale
     */
    public static function useFaker($locale = FakerFactory::DEFAULT_LOCALE) {
        static::setAttributeReplacer(new FakerAttributeReplacer(FakerFactory::create($locale)));
    }

}