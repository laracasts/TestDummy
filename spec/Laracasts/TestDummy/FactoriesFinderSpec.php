<?php

namespace spec\Laracasts\TestDummy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FactoriesFinderSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(__DIR__.'/helpers');
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Laracasts\TestDummy\FactoriesFinder');
	}

	function it_hunts_down_the_fixtures_file_like_a_dog()
	{
		$this->find()->shouldBe([__DIR__.'/helpers/all.php']);
	}

}
