<?php

namespace spec\Laracasts\TestDummy;

use Laracasts\TestDummy\AttributeReplacer;
use Laracasts\TestDummy\FixturesFinder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Laracasts\TestDummy\BuildableRepositoryInterface;
use stdClass;
use Symfony\Component\Yaml\Yaml;

class BuilderSpec extends ObjectBehavior {

    function let(BuildableRepositoryInterface $builderRepository, AttributeReplacer $attributeReplacer)
    {
        $fixtures = Yaml::parse(__DIR__.'/helpers/fixtures.yml');

        $this->beConstructedWith($builderRepository, $attributeReplacer, $fixtures);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Laracasts\TestDummy\Builder');
    }

    function it_fills_a_new_entity_with_attributes(BuildableRepositoryInterface $builderRepository, AttributeReplacer $attributeReplacer, StdClass $entity)
    {
        $builderRepository->build('Album', Argument::type('array'))->willReturn($entity);
        $attributeReplacer->replace(Argument::type('array'))->willReturn([]);

        $this->build('Album')->shouldReturn($entity);
    }

    function it_can_override_default_fixture(BuildableRepositoryInterface $builderRepository, AttributeReplacer $attributeReplacer)
    {
        $overrides = ['name' => 'Foobar'];

        $builderRepository->build('Album', $overrides)->willReturn($overrides);
        $attributeReplacer->replace(Argument::type('array'))->willReturn($overrides);

        $this->build('Album', $overrides)->shouldReturn($overrides);
    }

    function it_can_make_and_persist_an_entity(BuildableRepositoryInterface $builderRepository, AttributeReplacer $attributeReplacer)
    {
        $albumStub = new AlbumStub;

        $builderRepository->build(Argument::type('string'), Argument::type('array'))->willReturn($albumStub);
        $builderRepository->save($albumStub)->shouldBeCalled();
        $attributeReplacer->replace(Argument::type('array'))->willReturn([]);

        $this->create('Album')->shouldReturn($albumStub);
    }

    function it_can_create_multiple_entities_at_once(BuildableRepositoryInterface $builderRepository, AttributeReplacer $attributeReplacer)
    {
        $stub = new AlbumStub;

        $builderRepository->build(Argument::type('string'), Argument::type('array'))->shouldBeCalledTimes(3)->willReturn($stub);
        $builderRepository->save($stub)->shouldBeCalledTimes(3);
        $attributeReplacer->replace(Argument::type('array'))->willReturn([]);

        $this->setTimes(3)->create('Album')->shouldReturn([$stub, $stub, $stub]);
    }

}

class AlbumStub {
    public function getAttributes()
    {
       return ['name' => 'Back in Black'];
    }
}
