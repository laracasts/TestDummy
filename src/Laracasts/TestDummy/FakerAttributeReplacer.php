<?php

namespace Laracasts\TestDummy;

use DateTime;
use Faker\Generator;
use InvalidArgumentException;

class FakerAttributeReplacer implements AttributeReplacer {

    /**
     * Perform dynamic replacements of text
     *
     * @param Generator $fake
     */
    function __construct(Generator $fake)
    {
        $this->fake = $fake;
    }

    /**
     * Search through an array of attributes,
     * and perform any applicable replacements.
     *
     * @param array $data
     * @return array
     */
    public function replace(array $data)
    {
        foreach ($data as $column => $value) {
            $data[$column] = $this->updateColumnValue($value);
        }

        return $data;
    }

    /**
     * Update any placeholders with dynamic fake substitutes.
     *
     * @param $value
     * @return mixed
     */
    protected function updateColumnValue($value)
    {
        return preg_replace_callback(
            '/\$([a-zA-Z0-9]+)/',
            function ($matches) {
                $fakeType = $matches[1];
                try {
                    return $this->transformFakeObject($this->fake->$fakeType);
                } catch (InvalidArgumentException $exception) {
                    // If we don't recognize it, we'll just keep it as it is.
                    return $matches[0];
                }
            },
            $value
        );
    }

    protected function transformFakeObject($fakeObject)
    {
        if ($fakeObject instanceof DateTime) {
            return $fakeObject->format('Y-m-d H:i:s');
        }
        return (string)$fakeObject;
    }

}