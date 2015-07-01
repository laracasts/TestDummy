<?php

namespace Laracasts\TestDummy;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class FactoriesFinder
{

    /**
     * The base directory to conduct the search.
     *
     * @var string
     */
    private $basePath;

    /**
     * Create a new FixturesFinder instance.
     *
     * @param string $basePath
     */
    function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Fetch an array of factory files.
     *
     * @return array
     */
    public function find()
    {
        $iterator = $this->getDirectoryIterator();
        $iterator = new RegexIterator($iterator, '#^.*\.(php)+$#Di');

        return array_keys(iterator_to_array($iterator));
    }

    /**
     * Get the directory iterator.
     *
     * @return RecursiveIteratorIterator
     */
    private function getDirectoryIterator()
    {
        $directoryIterator = new RecursiveDirectoryIterator($this->basePath);
        $directoryIterator->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);

        return new RecursiveIteratorIterator($directoryIterator);
    }
}
