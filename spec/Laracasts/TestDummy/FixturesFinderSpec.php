<?php

namespace spec\Laracasts\TestDummy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FixturesFinderSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(__DIR__);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Laracasts\TestDummy\FixturesFinder');
	}

	function it_hunts_down_the_fixtures_file_like_a_dog()
	{
		$this->find()->shouldMatch('helpers/fixtures.yml');
	}

	function it_throws_an_exception_if_the_fixtures_file_is_not_found()
	{
		$this->beConstructedWith(__DIR__, 'doesntexistfile');

		$this->shouldThrow('Laracasts\TestDummy\TestDummyException')
			 ->duringFind();
	}

	public function getMatchers()
	{
		return [
			'match' => function($actual, $expected)
			{
				return preg_match('%' . $expected . '%', $actual);
			}
		];
	}
}
