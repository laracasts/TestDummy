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
	 * Create a new Builder instance.
	 *
	 * @return Builder
	 */
	protected static function getInstance()
	{
		$finder = new FixturesFinder(app_path('tests'));
		$fixtures = Yaml::parse($finder->find());

		return new Builder(new EloquentDatabaseProvider, $fixtures);
	}

	/**
	 * Fill an entity with test data, without saving it.
	 *
	 * @param string $type
	 * @param array  $attributes
	 * @return array
	 */
	public static function make($type, array $attributes = [])
	{
		return static::getInstance()->make($type, $attributes);
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