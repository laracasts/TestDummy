<?php

namespace spec\Laracasts\TestDummy;
use Faker\Factory as Faker;

use Laracasts\TestDummy\FixturesFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laracasts\TestDummy\BuildableRepositoryInterface;
use stdClass;
use Laracasts\TestDummy\Factory;
use Illuminate\Support\Collection;

class BuilderSpec extends ObjectBehavior {

    function let(BuildableRepositoryInterface $builderRepository)
    {
        $factories = (new Factory(__DIR__.'/helpers'))->factories();
        $faker = Faker::create();

        $this->beConstructedWith($builderRepository, $factories, $faker);
    }

    function it_gets_attributes_for_a_model()
    {
        $attributes = $this->attributesFor('Album', ['artist' => 'AC/DC']);

        $attributes['name']->shouldBeString();
        $attributes['artist']->shouldBe('AC/DC');
    }

    function it_builds_entities(BuildableRepositoryInterface $builderRepository)
    {
        $builderRepository->build('Album', Argument::type('array'))->willReturn('foo');

        $this->build('Album')->shouldReturn('foo');
    }

    function it_can_override_defaults_from_factory(BuildableRepositoryInterface $builderRepository)
    {
        $overrides = ['artist' => 'Captain Geech and the Shrimp-Shack Shooters', 'name' => 'Album Name'];

        $builderRepository->build('Album', $overrides)->willReturn($overrides);

        $this->build('Album', $overrides)->shouldReturn($overrides);
    }

    function it_can_handle_attributes_returned_from_closure(BuildableRepositoryInterface $builderRepository)
    {
        $builderRepository->build('Artist', Argument::type('array'))->willReturn('foo');

        $this->build('Artist')->shouldReturn('foo');
    }

    function it_can_override_defaults_in_a_closure(BuildableRepositoryInterface $builderRepository)
    {
        $overrides = ['name' => 'The Boogaloos'];

        $builderRepository->build('Artist', $overrides)->willReturn($overrides);

        $this->build('Artist', $overrides)->shouldReturn($overrides);
    }

    function it_can_persist_an_entity(BuildableRepositoryInterface $builderRepository)
    {
        $albumStub = new AlbumStub;

        $builderRepository->getAttributes(Argument::any())->willReturn([]);
        $builderRepository->build('Album', Argument::type('array'))->willReturn($albumStub);
        $builderRepository->save($albumStub)->shouldBeCalled();

        $this->create('Album')->shouldReturn($albumStub);
    }

    function it_can_create_multiple_entities_at_once(BuildableRepositoryInterface $builderRepository)
    {
        $stub = new AlbumStub;

        $builderRepository->getAttributes(Argument::any())->willReturn([]);
        $builderRepository->build('Album', Argument::type('array'))->shouldBeCalledTimes(3)->willReturn($stub);
        $builderRepository->save($stub)->shouldBeCalledTimes(3);

        $collection = $this->setTimes(3)->create('Album');

        $collection[0]->shouldEqual($stub);
        $collection[1]->shouldEqual($stub);
        $collection[2]->shouldEqual($stub);
    }

}

class AlbumStub {
    public function getAttributes()
    {
       return ['name' => 'Back in Black', 'artist' => 'AC/DC'];
    }
}
