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

        $this->assertInstanceOf('Foo', $attributes);
        $this->assertEquals('bar', $attributes->name);
    }

    /** @test */
    public function it_allows_for_overriding_attributes()
    {
        $attributes = TestDummy::build('Foo', ['name' => 'override']);

        $this->assertEquals('override', $attributes->name);
    }
}
