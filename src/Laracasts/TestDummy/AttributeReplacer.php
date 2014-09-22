<?php namespace Laracasts\TestDummy;

interface AttributeReplacer {

    /**
     * Search through an array of attributes,
     * and perform any applicable replacements.
     *
     * @param array $data
     * @return array
     */
    public function replace(array $data);

}