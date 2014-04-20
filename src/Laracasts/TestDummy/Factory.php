<?php namespace Laracasts\TestDummy;

/**
 * Convenience Laravel entry point to Builder.
 * Factory::times(2)->create('Post')
 *
 * @package Laracasts\TestDummy
 */
class Factory {

	/**
	 * Create and save test data
	 *
	 * @param int $times
	 */
	function __construct($times = 1)
	{
		$this->times = $times;
	}

	/**
	 * Create a new Builder instance.
	 *
	 * @return Builder
	 */
	protected static function getInstance()
	{
		$finder = new FixturesFinder(app_path('tests'));

		return new Builder(new EloquentDatabaseProvider, $finder);
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