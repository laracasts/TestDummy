<?php

namespace spec\Laracasts\TestDummy;

use Laracasts\TestDummy\FixturesFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laracasts\TestDummy\BuildableRepositoryInterface;
use stdClass;
use Symfony\Component\Yaml\Yaml;

class BuilderSpec extends ObjectBehavior {

    function let(BuildableRepositoryInterface $builderRepository)
    {
        $fixtures = Yaml::parse(__DIR__.'/helpers/fixtures.yml');

        $this->beConstructedWith($builderRepository, $fixtures);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Laracasts\TestDummy\Builder');
    }

    function it_fills_a_new_entity_with_attributes(BuildableRepositoryInterface $builderRepository, StdClass $entity)
    {
        $builderRepository->build('Album', Argument::type('array'))->willReturn($entity);

        $this->build('Album')->shouldReturn($entity);
    }

    function it_can_override_default_fixture(BuildableRepositoryInterface $builderRepository)
    {
        $overrides = ['name' => 'Foobar'];

        $builderRepository->build('Album', $overrides)->willReturn($overrides);

        $this->build('Album', $overrides)->shouldReturn($overrides);
    }

    function it_does_not_set_fields_that_do_not_exist_in_the_fixtures_yaml_file_declaration(BuildableRepositoryInterface $builderRepository)
    {
        $overrides = ['name' => 'Foobar', 'shouldNot' => 'beIncluded'];
        $expected = ['name' => 'Foobar'];

        $builderRepository->build('Album', $expected)->willReturn($expected);

        $this->build('Album', $overrides)->shouldReturn($expected);
    }

    function it_can_make_and_persist_an_entity(BuildableRepositoryInterface $builderRepository)
    {
        $albumStub = new AlbumStub;

        $builderRepository->build(Argument::type('string'), Argument::type('array'))->willReturn($albumStub);
        $builderRepository->save($albumStub)->shouldBeCalled();

        $this->create('Album')->shouldReturn($albumStub);
    }

    function it_can_create_multiple_entities_at_once(BuildableRepositoryInterface $builderRepository)
    {
        $stub = new AlbumStub;

        $builderRepository->build(Argument::type('string'), Argument::type('array'))->shouldBeCalledTimes(3)->willReturn($stub);
        $builderRepository->save($stub)->shouldBeCalledTimes(3);

        $this->setTimes(3)->create('Album')->shouldReturn([$stub, $stub, $stub]);
    }

}

class AlbumStub {
    public function getAttributes()
    {
       return ['name' => 'Back in Black', 'artist' => 'AC/DC'];
    }
}
