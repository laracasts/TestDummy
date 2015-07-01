<?php

namespace Laracasts\TestDummy;

use Symfony\Component\Process\Exception\LogicException;

class Definition
{

    /**
     * The class name for the factory.
     *
     * @var string
     */
    protected $name;

    /**
     * The abbreviated short-name.
     *
     * @var string
     */
    protected $shortName;

    /**
     * Attributes for the factory.
     *
     * @var array
     */
    protected $attributes;


    /**
     * Create a new Definition instance.
     *
     * @param string $name
     * @param string $shortName
     * @param array  $attributes
     */
    public function __construct($name, $shortName, $attributes = [])
    {
        $this->name = $name;
        $this->shortName = $shortName;
        $this->attributes = $attributes;
    }

    public function __set($attribute, $value)
    {
        //We want to keep the object always in the same state
        throw new LogicException('You cannot change any value of this object');
    }

    public function __get($attribute)
    {
        return $this->{$attribute};
    }
}
