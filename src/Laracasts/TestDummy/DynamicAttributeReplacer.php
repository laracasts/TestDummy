<?php namespace Laracasts\TestDummy;

use Faker\Factory as Faker;

class DynamicAttributeReplacer {

	/**
	 * We'll increment all numbers references
	 * to ensure that the same one isn't used.
	 *
	 * @var integer
	 */
	protected static $number;

	/**
	 * @var Faker
	 */
	protected $fake;

	/**
	 * Supported fake types
	 *
	 * @var array
	 */
	protected $matches = [
		'string', 'integer'
	];

	/**
	 * Perform dynamic replacements of text
	 */
	function __construct()
	{
		$this->fake = Faker::create();
	}

	/**
	 * Search through an array of attributes,
	 * and perform any applicable replacements.
	 *
	 * @param array $data
	 * @return array
	 */
	public function replace(array $data)
	{
		foreach ($data as $column => $value)
		{
			$data[$column] = preg_replace_callback('/\$([a-z]+)/', function($matches)
			{
				if ($this->isASupportedFakeType($fakeType = $matches[1]))
				{
					return call_user_func([$this, 'getFake' . ucwords($fakeType)]);
				}

				// If we don't recognize it, we'll just keep it as it is.
				return $matches[0];
			}, $value);
		}

		return $data;
	}

	/**
	 * Determine if the provided type is a supported fake type.
	 *
	 * @param $fakeType
	 * @return bool
	 */
	protected function isASupportedFakeType($fakeType)
	{
		return in_array($fakeType, $this->matches);
	}

	/**
	 * Get a fake string
	 *
	 * @return string
	 */
	protected function getFakeString()
	{
	   return $this->fake->word;
	}

	/**
	 * Get a fake integer
	 *
	 * @return mixed
	 */
	protected function getFakeInteger()
	{
		return static::$number += 1;
	}
}