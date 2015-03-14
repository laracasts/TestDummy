<?php

use Laracasts\TestDummy\Factory as TestDummy;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        TestDummy::$factoriesPath = __DIR__ . '/support/factories';
    }

    /** @test */
    public function it_builds_up_attributes_for_an_entity()
    {
        $attributes = TestDummy::build('Foo');

        assertInstanceOf('Foo', $attributes);
        assertEquals('bar', $attributes->name);
    }

    /** @test */
    public function it_allows_for_overriding_attributes()
    {
        $attributes = TestDummy::build('Foo', ['name' => 'override']);

        assertEquals('override', $attributes->name);
    }

    /** @test */
    public function it_gets_an_array_only_of_attributes()
    {
        $attributes = TestDummy::attributesFor('Foo', ['name' => 'override']);

        assertInternalType('array', $attributes);
        assertEquals('override', $attributes['name']);
    }
}
