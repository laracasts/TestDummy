<?php

use \Illuminate\Database\Eloquent\Model;

class Foo extends Model
{
    public static function createFoo($name)
    {
        $foo = new Foo();
        $foo->name = $name;

        return $foo;
    }

    /**
     * @param string $name
     */
    protected function setNameAttribute($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('The name should be a string');
        }

        $this->attributes['name'] = $name;
    }
}