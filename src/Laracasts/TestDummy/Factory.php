<?php namespace Laracasts\TestDummy;

use Symfony\Component\Yaml\Yaml;

/**
 * Convenience Laravel entry point to Builder.
 * Factory::times(2)->create('Post')
 *
 * @package Laracasts\TestDummy
 */
class Factory {

	/**
	 * The path to the fixtures file
	 *
	 * @var string
	 */
	protected static $fixturesPath;

	/**
	 * Create a new Builder instance.
	 *
	 * @return Builder
	 */
	protected static function getInstance()
	{
		if ( ! static::$fixturesPath)
		{
			$finder = new FixturesFinder(app_path('tests'));

			static::$fixturesPath = Yaml::parse($finder->find());
		}

		return new Builder(new EloquentDatabaseProvider, static::$fixturesPath);
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

} 