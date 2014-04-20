<?php

namespace spec\Laracasts\TestDummy;

use Laracasts\TestDummy\FixturesFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laracasts\TestDummy\BuildableRepositoryInterface;
use stdClass;

class BuilderSpec extends ObjectBehavior {

    function let(BuildableRepositoryInterface $builderRepository)
    {
        $fixturesFinder = new FixturesFinder(__DIR__);

        $this->beConstructedWith($builderRepository, $fixturesFinder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Laracasts\TestDummy\Builder');
    }

    function it_fills_a_new_entity_with_attributes(BuildableRepositoryInterface $builderRepository, StdClass $entity)
    {
        $builderRepository->make('Album', Argument::type('array'))->willReturn($entity);

        $this->make('Album')->shouldReturn($entity);
    }

    function it_can_override_default_fixture(BuildableRepositoryInterface $builderRepository)
    {
        $overrides = ['name' => 'Foobar'];

        $builderRepository->make('Album', $overrides)->willReturn($overrides);

        $this->make('Album', $overrides)->shouldReturn($overrides);
    }

    function it_can_make_and_persist_an_entity(BuildableRepositoryInterface $builderRepository)
    {
        $albumStub = new AlbumStub;

        $builderRepository->make(Argument::type('string'), Argument::type('array'))->willReturn($albumStub);
        $builderRepository->save($albumStub)->shouldBeCalled();

        $this->create('Album')->shouldReturn($albumStub);
    }

    function it_can_create_multiple_entities_at_once(BuildableRepositoryInterface $builderRepository)
    {
        $stub = new AlbumStub;

        $builderRepository->make(Argument::type('string'), Argument::type('array'))->shouldBeCalledTimes(3)->willReturn($stub);
        $builderRepository->save($stub)->shouldBeCalledTimes(3);

        $this->setTimes(3)->create('Album')->shouldReturn([$stub, $stub, $stub]);
    }

}

class AlbumStub {
    public function getAttributes()
    {
       return ['name' => 'Back in Black'];
    }
}
