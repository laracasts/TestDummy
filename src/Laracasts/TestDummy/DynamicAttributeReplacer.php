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
     * The Faker instance
     *
	 * @var Faker
	 */
	protected $fake;

	/**
	 * Supported fake types
	 *
	 * @var array
	 */
	protected $supportedFakes = [
		'string', 'integer', 'date', 'text', 'boolean', 'uuid'
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
			$data[$column] = $this->updateColumnValue($value);
		}

		return $data;
	}

	/**
	 * Update any placeholders with dynamic fake substitutes.
	 *
	 * @param $value
	 * @return mixed
	 */
	protected function updateColumnValue($value)
	{
		return preg_replace_callback('/\$([a-z]+)/', function($matches)
		{
			if ($this->isASupportedFakeType($fakeType = $matches[1]))
			{
				return call_user_func([$this, 'getFake' . ucwords($fakeType)]);
			}

			// If we don't recognize it, we'll just keep it as it is.
			return $matches[0];
		}, $value);
	}

	/**
	 * Determine if the provided type is a supported fake type.
	 *
	 * @param $fakeType
	 * @return bool
	 */
	protected function isASupportedFakeType($fakeType)
	{
		return in_array($fakeType, $this->supportedFakes);
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

	/**
	 * Get a fake MySQL timestamp
	 *
	 * @return mixed
	 */
	protected function getFakeDate()
	{
		return $this->fake->dateTimeThisYear->format('Y-m-d H:i:s');
	}

	/**
	 * Get a fake paragraph
	 *
	 * @return mixed
	 */
	protected function getFakeText()
	{
		return $this->fake->paragraph(4);
	}
	
	/**
	 * Get a fake boolean
	 * 
	 * @return mixed
	 */
	protected function getFakeBoolean()
	{
		return (int) $this->fake->boolean();
	}
	
	 /**
         * Get a fake uuid
         *
         * @return string
         */
        protected function getFakeUuid()
        {
           	return $this->fake->uuid;
        }

}
