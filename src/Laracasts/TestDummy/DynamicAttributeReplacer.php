<?php namespace Laracasts\TestDummy;

use Exception;
use Faker\Factory as Faker;

class DynamicAttributeReplacer {

    /**
     * We'll increment all numbers references
     * to ensure that the same one isn't used.
     *
     * @var integer
     */
    protected static $number;

    /**
     * The Faker instance.
     *
     * @var Faker
     */
    protected $fake;

    /**
     * All supported fake types.
     *
     * @var array
     */
    protected $supportedFakes = [
        'string', 'integer', 'digit', 'date', 'text', 'boolean', 'uuid'
    ];

    /**
     * Create a new DynamicAttributeReplacer instance.
     */
    function __construct()
    {
        $this->fake = Faker::create();
    }

    /**
     * Search an array of attributes, and perform any
     * applicable replacements.
     *
     * @param array $data
     * @return array
     */
    public function replace(array $data)
    {
        foreach ($data as $column => $value)
        {
            if (is_string($value))
            {
                $data[$column] = $this->updateColumnValue($value);
            }
        }

        return $data;
    }

    /**
     * Update any placeholders with fake substitutes.
     *
     * @param $value
     * @return mixed
     */
    protected function updateColumnValue($value)
    {
        return preg_replace_callback('/\$([a-zA-Z]+)/', function ($matches)
        {
            return $this->getFake($matches[0], $matches[1]);
        }, $value);
    }

    /**
     * Get a fake substitute for the given variable.
     *
     * @param $original
     * @param $fakeType
     * @return mixed
     */
    public function getFake($original, $fakeType)
    {
        // We'll first see if TestDummy recognizes the requested fake type.
        if ($this->isASupportedFakeType($fakeType))
        {
            return call_user_func([$this, 'getFake' . ucwords($fakeType)]);
        }

        // Otherwise, we'll fallback to using Faker's API. And, if Faker
        // doesn't recognize it, we'll just keep it as it is.
        try
        {
            return $this->fake->$fakeType;
        }
        catch(Exception $e)
        {
            return $original;
        }
    }

    /**
     * Determine if the provided type is a supported fake type.
     *
     * @param $fakeType
     * @return bool
     */
    protected function isASupportedFakeType($fakeType)
    {
        return in_array($fakeType, $this->supportedFakes);
    }

    /**
     * Get a fake string.
     *
     * @return string
     */
    protected function getFakeString()
    {
        return $this->fake->word;
    }

    /**
     * Get a fake integer.
     *
     * @return mixed
     */
    protected function getFakeInteger()
    {
        return static::$number += 1;
    }

    /**
     * Get a fake digit.
     *
     * @return mixed
     */
    protected function getFakeDigit()
    {
        return rand(0, 9);
    }

    /**
     * Get a fake MySQL timestamp.
     *
     * @return mixed
     */
    protected function getFakeDate()
    {
        return $this->fake->dateTimeThisYear->format('Y-m-d H:i:s');
    }

    /**
     * Get a fake paragraph.
     *
     * @return mixed
     */
    protected function getFakeText()
    {
        return $this->fake->paragraph(4);
    }

    /**
     * Get a fake boolean.
     *
     * @return mixed
     */
    protected function getFakeBoolean()
    {
        return (int) $this->fake->boolean();
    }

    /**
     * Get a fake uuid.
     *
     * @return string
     */
    protected function getFakeUuid()
    {
        return $this->fake->uuid;
    }

}
