<?php namespace Laracasts\TestDummy;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FixturesFinder {

	/**
	 * The base directory for the search
	 *
	 * @var string
	 */
	static $basePath;

	/**
	 * Track down the fixtures.yml file
	 */
	function __construct($basePath = null)
	{
		static::$basePath = $basePath ?: app_path('tests');
	}

	/**
	 * Track down the fixtures file for TestDummy
	 *
	 * @throws TestDummyException
	 * @return mixed
	 */
	public function find()
	{
		$testsDirectory = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(static::$basePath)
		);

		// We'll try to hunt down the fixtures.yaml file.
		// This way, the file can be placed just about anywhere.
		foreach($testsDirectory as $file)
		{
			$name = $file->getFilename();

			if ($name == 'fixtures.yml' or $name == 'fixtures.yaml')
			{
				return $file->getPathname();
			}
		}

		throw new TestDummyException('Could not locate the fixtures.yml file.');
	}
}