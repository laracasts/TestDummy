<?php

namespace spec\Laracasts\TestDummy;

use DateTime;
use Faker\Generator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FakerAttributeReplacerSpec extends ObjectBehavior {

    function let(Generator $faker) {
        $this->beConstructedWith($faker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Laracasts\TestDummy\FakerAttributeReplacer');
    }

    function it_replaces_placeholders(Generator $faker)
    {
        $name = 'Dr. Zane Stroman';
        $birthdate = new DateTime('1915-05-30 19:28:21');
        $address = '8888 Cummings Vista Apt. 101, Susanbury, NY 95473';
        $phone = '132-149-0269x3767';
        $email = 'tkshlerin@collins.com';

        $faker->name = $name;
        $faker->dateTimeThisCentury = $birthdate;
        $faker->address = $address;
        $faker->phoneNumber = $phone;
        $faker->email = $email;

        $replaced = $this->replace(
            [
                'name' => '$name',
                'birthdate' => '$dateTimeThisCentury',
                'address' => '$address',
                'phone' => '$phoneNumber',
                'email' => '$email',
            ]
        );

        $replaced->shouldBeArray();
        $replaced['name']->shouldEqual($name);
        $replaced['birthdate']->shouldEqual($birthdate->format('Y-m-d H:i:s'));
        $replaced['address']->shouldEqual($address);
        $replaced['phone']->shouldEqual($phone);
        $replaced['email']->shouldEqual($email);
    }

}