<?php namespace Laracasts\TestDummy;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FixturesFinder {

	/**
	 * The base directory for the search
	 *
	 * @var string
	 */
	protected $basePath;

	/**
	 * @var string
	 */
	private $fixturesFileName;

	/**
	 * Track down the fixtures.yml file
	 */
	function __construct($basePath, $fixturesFileName = 'fixtures')
	{
		$this->basePath = $basePath;
		$this->fixturesFileName = $fixturesFileName;
	}

	/**
	 * Track down the fixtures file for TestDummy.
	 * This way, the file can be placed just about anywhere
	 *
	 * @throws TestDummyException
	 * @return mixed
	 */
	public function find()
	{
		foreach($this->getDirectoryIterator() as $file)
		{
			$name = $file->getFilename();

			if ($this->isTheFixturesFile($name)) return $file->getPathname();
		}

		throw new TestDummyException('Could not locate the fixtures.yml file.');
	}

	/**
	 * Get the directory iterator
	 *
	 * @return RecursiveIteratorIterator
	 */
	protected function getDirectoryIterator()
	{
		return new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($this->basePath)
		);
	}

	/**
	 * Is the given file the one we want?
	 *
	 * @param $name
	 * @return bool
	 */
	protected function isTheFixturesFile($name)
	{
		$fixturesName = $this->fixturesFileName;

		return preg_match('/' . $fixturesName . '.ya?ml$/i', $name);
	}
}