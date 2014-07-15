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
		'boolean',
		'date',
		'integer',
		'ip',
		'ipv4',
		'ipv6',
		'slug',
		'string',
		'text',
		'username',
		'uuid',
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
		return preg_replace_callback('/\$([a-z]+)/', function ($matches)
		{
			if ($this->isASupportedFakeType($fakeType = $matches[1]))
			{
				return call_user_func([ $this, 'getFake' . ucwords($fakeType) ]);
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

	/**
	 * Get a fake ip (random ipv4 or ipv6)
	 *
	 * @property \Faker\Provider\Internet ipv4()
	 * @example '237.149.115.38'
	 * -
	 * @property \Faker\Provider\Internet ipv6()
	 * @example '35cd:186d:3e23:2986:ef9f:5b41:42a4:e6f1'
	 * @return mixed
	 */
	protected function getFakeIp()
	{
		$bool = rand(0, 1);
		if ($bool) {
			return $this->getFakeIpv6();
		}

		return $this->getFakeIpv4();
	}

	/**
	 * Get a fake ipv6
	 *
	 * @property \Faker\Provider\Internet ipv6()
	 * @example '35cd:186d:3e23:2986:ef9f:5b41:42a4:e6f1'
	 * @return mixed
	 */
	protected function getFakeIpv6()
	{
		return $this->fake->ipv6();
	}

	/**
	 * Get a fake ipv4
	 *
	 * @property \Faker\Provider\Internet ipv4()
	 * @example '237.149.115.38'
	 * @return mixed
	 */
	protected function getFakeIpv4()
	{
		return $this->fake->ipv4();
	}

	/**
	 * Get a fake username
	 *
	 * @property \Faker\Provider\Internet userName()
	 * @example 'jdoe'
	 * @return mixed
	 */
	protected function getFakeUsername()
	{
		return $this->fake->userName();
	}

	/**
	 * Get a fake slug
	 *
	 * @property \Faker\Provider\Internet slug($nbWords = 6, $variableNbWords = true)
	 * @example 'aut-repellat-commodi-vel'
	 * @return mixed
	 */
	protected function getFakeSlug()
	{
		return $this->fake->slug(4);
	}
}
