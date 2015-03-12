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

	function it_ignores_non_php_files()
	{
		$dir = __DIR__.'/helpers';
		$notPhpFile = $dir .'/foo.txt';

		// We'll create a file that should not be included...
		file_put_contents($notPhpFile, '');

		$this->find()->shouldBe([$dir.'/all.php']);

		unlink($notPhpFile);
	}

}
