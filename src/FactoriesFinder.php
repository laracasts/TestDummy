<?php

namespace Laracasts\TestDummy;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
        $files = [];

        foreach ($this->getDirectoryIterator() as $file) {
            $extension = pathinfo($file)['extension'];

            if ($extension !== 'php') continue;

            $files[] = $file->getPathname();
        }

        return $files;
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
