<?php

use Illuminate\Database\Capsule\Manager as DB;
use Laracasts\TestDummy\Factory;

class FaktoryCreateTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        Factory::$factoriesPath = __DIR__ . '/factories';
    }

    /** @test */
    public function it_can_create_override_attributes_while_there_is_a_defined_relationship()
    {
        $user = Factory::create('user_with_role', [
            'email' => 'example@example.com',
            'password' => 'my-secret-password',
        ]);

        $this->assertEquals('example@example.com', $user->email);
        $this->assertEquals('my-secret-password', $user->password);
        $this->assertInstanceOf('Role', $user->role);
    }
}
