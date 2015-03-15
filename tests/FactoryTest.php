<?php

use Laracasts\TestDummy\Factory as TestDummy;

use Illuminate\Database\Capsule\Manager as DB;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        TestDummy::$factoriesPath = __DIR__ . '/support/factories';

        $this->setUpDatabase();
        $this->migrateTables();
    }

    protected function setUpDatabase()
    {
        $db = new DB;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);

        $db->bootEloquent();
        $db->setAsGlobal();
    }

    protected function migrateTables()
    {
        DB::schema()->create('posts', function ($table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });

        DB::schema()->create('comments', function ($table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->string('body');
            $table->timestamps();
        });
    }

    /** @test */
    public function it_builds_up_attributes_for_an_entity()
    {
        $attributes = TestDummy::build('Post');

        assertInstanceOf('Post', $attributes);
        assertEquals('Post Title', $attributes->title);
    }

    /** @test */
    public function it_allows_for_overriding_attributes()
    {
        $post = TestDummy::build('Post', ['title' => 'override']);

        assertEquals('override', $post->title);
    }

    /** @test */
    public function it_accepts_a_short_name_identifier_instead_of_the_model_class()
    {
        $post = TestDummy::build('scheduled_post');

        assertInstanceOf('Post', $post);
    }

    /** @test */
    public function it_allows_a_closure_to_be_used_for_defining_factories()
    {
        $foo = TestDummy::build('Foo');

        assertInstanceOf('Foo', $foo);
        assertInternalType('string', $foo->name);
    }

    /** @test */
    public function it_gets_an_array_only_of_attributes()
    {
        $attributes = TestDummy::attributesFor('Post', ['title' => 'override']);

        assertInternalType('array', $attributes);
        assertEquals('override', $attributes['title']);
    }

    /** @test */
    public function it_builds_and_persists_attributes()
    {
        $post = TestDummy::create('Post');

        assertInstanceOf('Post', $post);
        assertNotNull($post->id);
    }

    /** @test */
    public function it_builds_up_relationships_if_specified()
    {
        $comment = TestDummy::create('Comment');

        assertInstanceOf('Comment', $comment);
        assertInstanceOf('Post', $comment->post);
    }

    /** @test */
    public function it_can_build_and_persist_multiple_times()
    {
        $posts = TestDummy::times(3)->create('Post');

        assertInstanceOf('Illuminate\Support\Collection', $posts);
        assertCount(3, $posts);
    }

    /**
     * @test
     * @expectedException Laracasts\TestDummy\TestDummyException
     */
    public function it_squawks_if_you_try_to_build_an_unknown()
    {
        TestDummy::attributesFor('ClassThatDoesNotExist');
    }
}
