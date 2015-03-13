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
        $this->assertInstanceOf('Acme\Role', $user->role);
    }

    /** @test */
    public function relationship_attributes_can_be_overridden_using_dot_notation_for_named_factories()
    {
        $user = Factory::create('user_with_role', [
            'email' => 'example@example.com',
            'password' => 'my-secret-password',
            'role.display_name' => 'role-name',
        ]);

        $this->assertEquals('example@example.com', $user->email);
        $this->assertEquals('my-secret-password', $user->password);
        $this->assertEquals('role-name', $user->role->display_name);
    }

    /** @test */
    public function relationship_attributes_can_be_overridden_using_dot_notation_for_namespaced_factories()
    {
        $user = Factory::create('Acme\User', [
            'email' => 'example@example.com',
            'password' => 'my-secret-password',
            'Acme\Role.display_name' => 'role-name',
        ]);

        $this->assertEquals('example@example.com', $user->email);
        $this->assertEquals('my-secret-password', $user->password);
        $this->assertEquals('role-name', $user->role->display_name);
    }
}
