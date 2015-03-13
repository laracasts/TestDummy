<?php

namespace Laracasts\TestDummy;

use Illuminate\Support\Collection;

class Builder
{

    /**
     * All user-defined fixtures.
     *
     * @var array
     */
    protected $fixtures;

    /**
     * The number of times to create the record.
     *
     * @var integer
     */
    protected $times = 1;

    /**
     * A list of cached relationship ids.
     *
     * @var array
     */
    protected $relationIds = [];

    /**
     * The persistable model instance.
     *
     * @var IsPersistable
     */
    private $model;

    /**
     * Create a new Builder instance.
     *
     * @param IsPersistable  $model
     * @param array          $fixtures
     */
    public function __construct(IsPersistable $model, array $fixtures)
    {
        $this->model = $model;
        $this->fixtures = $fixtures;
    }

    /**
     * Get the number of times to create a record.
     *
     * @return integer
     */
    protected function getTimes()
    {
        return $this->times;
    }

    /**
     * Set the number of times to create records.
     *
     * @param  integer $count
     * @return $this
     */
    public function setTimes($count)
    {
        $this->times = $count;

        return $this;
    }

    /**
     * Get a single fixture.
     *
     * @param  string $name
     * @throws TestDummyException
     * @return mixed
     */
    public function getFixture($name)
    {
        // We'll first check to see if they gave us a short name.
        foreach ($this->fixtures as $fixture) {
            if ($fixture->shortName == $name) {
                return $fixture;
            }
        }

        // If not, we'll do a second sweep, and look for the class name.
        foreach ($this->fixtures as $fixture) {
            if ($fixture->name == $name) {
                return $fixture;
            }
        }

        throw new TestDummyException(
            'Could not locate a factory with the name: ' . $name
        );
    }

    /**
     * Build an array of dummy attributes for an entity.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return array
     */
    public function attributesFor($name, $attributes = [])
    {
        return $this->mergeFixtureWithOverrides($name, $attributes);
    }

    /**
     * Build up an entity and populate it with dummy data.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return array
     */
    public function build($name, $attributes = [])
    {
        $data = $this->mergeFixtureWithOverrides($name, $attributes);

        // We'll pass off the process of creating the entity.
        // That way, folks can use different persistence layers.
        return $this->model->build($this->getFixture($name)->name, $data);
    }

    /**
     * Build and persist a named entity.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return mixed
     */
    public function create($name, array $attributes = [])
    {
        $entities = array_map(function() use ($name, $attributes) {
            return $this->persist($name, $attributes);
        }, range(1, $this->getTimes()));

        return count($entities) > 1 ? new Collection($entities) : $entities[0];
    }

    /**
     * Merge the fixture with any potential overrides.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return array
     * @throws TestDummyException
     */
    protected function mergeFixtureWithOverrides($name, array $attributes)
    {
        $factory = $this->getFixture($name)->attributes;
        $factory = $this->triggerFakerOnAttributes($factory);

        return array_intersect_key($attributes, $factory) + $factory;
    }

    /**
     * Apply Faker dummy values to the attributes.
     *
     * @param  array $attributes
     * @return array
     */
    protected function triggerFakerOnAttributes(array $attributes)
    {
        // To ensure that we don't use the same Faker value for every
        // single factory of the same name, all Faker properties are
        // wrapped in closures.

        // So we can now filter through our attributes and call these
        // closures, which will generate the proper Faker values.

        return array_map(function ($attribute) {
            $attribute = is_callable($attribute) ? $attribute() : $attribute;

            // It's possible that the called Faker method returned an array.
            // If that is the case, we'll implode it for the user.

            return is_array($attribute) ? implode(' ', $attribute) : $attribute;
        }, $attributes);
    }

    /**
     * Persist the entity and any relationships.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return mixed
     */
    protected function persist($name, array $attributes = [])
    {
        $entity = $this->build($name, $attributes);
        $modelAttributes = $this->model->getAttributes($entity);

        // We'll filter through all of the columns, and check
        // to see if there are any defined relationships. If there
        // are, then we'll need to create those records as well.

        foreach ($modelAttributes as $columnName => $value) {
            if ($relationship = $this->hasRelationAttribute($value)) {
                $entity[$columnName] = $this->fetchRelationId($relationship, $attributes);
            }
        }

        $this->model->save($entity);

        return $entity;
    }

    /**
     * Check if the attribute refers to a relationship.
     *
     * @param  string $value
     * @return mixed
     */
    protected function hasRelationAttribute($value)
    {
        if (is_string($value) && preg_match('/^factory:(.+)$/i', $value, $matches)) {
            return $matches[1];
        }

        return false;
    }

    /**
     * Get the ID for the relationship.
     *
     * @param  string $relation
     * @param  array  $attributes
     * @return int
     */
    protected function fetchRelationId($relation, $attributes)
    {
        if ($this->isRelationAlreadyCreated($relation)) {
            return $this->relationIds[$relation];
        }

        $relationKey = $this->persist($relation, $attributes)->getKey();

        return $this->relationIds[$relation] = $relationKey;
    }

    /**
     * Determine if the provided relationship type has already been persisted.
     *
     * @param  string $relationshipType
     * @return bool
     */
    protected function isRelationAlreadyCreated($relationshipType)
    {
        return isset($this->relationIds[$relationshipType]);
    }

}
