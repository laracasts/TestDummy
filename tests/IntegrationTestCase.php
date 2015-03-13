<?php

use Illuminate\Database\Capsule\Manager as DB;

abstract class IntegrationTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->configureDatabase();
        $this->migrate();
    }

    protected function configureDatabase()
    {
        $db = new DB;
        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            ]);
        $db->bootEloquent();
        $db->setAsGlobal();
    }

    protected function migrate()
    {
        $this->migrateUsersTable();
        $this->migrateRolesTable();
    }


    protected function migrateUsersTable()
    {
        DB::schema()->create('users', function($table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->integer('role_id')->unsigned();
            $table->timestamps();
        });
    }

    protected function migrateRolesTable()
    {
        DB::schema()->create('roles', function($table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('display_name');
            $table->string('description');
            $table->timestamps();
        });
    }
}
