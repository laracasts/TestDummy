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
     * Build an array of dummy attributes for an entity.
     *
     * @param  string $name
     * @param  array  $overrides
     * @return array
     */
    public function attributesFor($name, $overrides = [])
    {
        return $this->getAttributes($name, $overrides);
    }

    /**
     * Build up an entity and populate it with dummy data.
     *
     * @param  string $name
     * @param  array  $overrides
     * @return array
     */
    public function build($name, $overrides = [])
    {
        $attributes = $this->getAttributes($name, $overrides);
        $class = $this->getFixture($name)->name;

        // We'll pass off the process of creating the entity.
        // That way, folks can use different persistence layers.

        return $this->model->build($class, $attributes);
    }

    /**
     * Build and persist a named entity.
     *
     * @param  string $name
     * @param  array  $overrides
     * @return mixed
     */
    public function create($name, array $overrides = [])
    {
        for ($i = 0; $i < $this->getTimes(); $i++) {
            $entities[] = $this->persist($name, $overrides);
        }

        if (count($entities) > 1) {
            return Collection::make($entities);
        }

        return $entities[0];
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

        $this->assignRelationships($entity);
        $this->model->save($entity);

        return $entity;
    }

    /**
     * Merge the fixture with any potential overrides.
     *
     * @param  string $name
     * @param  array  $attributes
     * @return array
     * @throws TestDummyException
     */
    protected function getAttributes($name, array $attributes)
    {
        $factory = $this->triggerFakerOnAttributes(
            $this->getFixture($name)->attributes
        );

        return array_merge($factory, $attributes);
    }

    /**
     * Get a single fixture.
     *
     * @param  string $name
     * @throws TestDummyException
     * @return mixed
     */
    protected function getFixture($name)
    {
        // The user may provide either a class name or a short
        // name identifier. So we'll track it down here.

        foreach ($this->fixtures as $fixture) {
            if ($fixture->shortName == $name) {
                return $fixture;
            }

            if ($fixture->name == $name && ! $fixture->shortName) {
                return $fixture;
            }
        }

        throw new TestDummyException(
            'Could not locate a factory with the name: ' . $name
        );
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
     * Prepare and assign any applicable relationships.
     *
     * @param  mixed $entity
     * @return mixed
     */
    protected function assignRelationships($entity)
    {
        $modelAttributes = $this->model->getAttributes($entity);

        // We'll filter through all of the columns, and check
        // to see if there are any defined relationships. If there
        // are, then we'll need to create those records as well.

        foreach ($modelAttributes as $columnName => $value) {
            if ($relationship = $this->findRelation($value)) {
                $entity[$columnName] = $this->fetchRelationId($relationship);
            }
        }

        return $entity;
    }

    /**
     * Check if the attribute refers to a relationship.
     *
     * @param  string $attribute
     * @return string|boolean
     */
    protected function findRelation($attribute)
    {
        if (is_string($attribute) && preg_match('/^factory:(.+)$/i', $attribute, $matches)) {
            return $matches[1];
        }

        return false;
    }

    /**
     * Get the ID for the relationship.
     *
     * @param  string $relation
     * @return int
     */
    protected function fetchRelationId($relation)
    {
        if ($this->isRelationAlreadyCreated($relation)) {
            return $this->relationIds[$relation];
        }

        $relationKey = $this->persist($relation)->getKey();

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
