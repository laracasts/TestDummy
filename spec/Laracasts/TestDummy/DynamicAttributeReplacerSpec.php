<?php

namespace spec\Laracasts\TestDummy;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DynamicAttributeReplacerSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('Laracasts\TestDummy\DynamicAttributeReplacer');
	}

	function it_updates_an_array_that_contains_placeholders()
	{
		$replaced = $this->replace([
			'title' => 'Some Post Number $integer',
			'body' => '$text',
			'excerpt' => 'Some excerpt with $string',
			'author' => '$invalid placeholder'
		]);

		$replaced->shouldBeArray();

		$replaced['title']->shouldEqual('Some Post Number 1');
		$replaced['body']->shouldNotEqual('$text');
		$replaced['excerpt']->shouldMatch('Some excerpt with [a-z]+$');
		$replaced['author']->shouldEqual('$invalid placeholder');
	}

    function it_will_fallback_to_faker_properties()
    {
        $replaced = $this->replace([
            'address' => '$address'
        ]);

        $replaced['address']->shouldNotEqual('$address');
    }

	public function getMatchers()
	{
		return [
			'match' => function($output, $input)
			{
				return preg_match('/' . $input . '/', $output);
			}
		];
	}
}
