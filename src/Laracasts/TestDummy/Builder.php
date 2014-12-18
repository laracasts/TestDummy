<?php namespace Laracasts\TestDummy;

use Illuminate\Support\Collection;

class Builder {

    /**
     * All user-defined fixtures.
     *
     * @var array
     */
    protected $fixtures;

    /**
     * The number of times to create the record.
     *
     * @var int
     */
    protected $times = 1;

    /**
     * A list of cached relationship ids.
     *
     * @var array
     */
    protected $relationshipIds = [];

    /**
     * The buildable repository layer.
     *
     * @var BuildableRepositoryInterface
     */
    private $database;

    /**
     * Create a new Builder instance.
     *
     * @param BuildableRepositoryInterface $database
     * @param array $fixtures
     */
    public function __construct(BuildableRepositoryInterface $database, array $fixtures)
    {
        $this->database = $database;
        $this->fixtures = $fixtures;
    }

    /**
     * Get the number of times to create a record.
     *
     * @return int
     */
    protected function getTimes()
    {
        return $this->times;
    }

    /**
     * Set the number of times to create records.
     *
     * @param $count
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
     * @param string $name
     * @throws TestDummyException
     * @return mixed
     */
    public function getFixture($name)
    {
        // We'll first check to see if they gave us a short name.
        foreach ($this->fixtures as $fixture)
        {
            if ($fixture->shortName == $name)
            {
                return $fixture;
            }
        }

        // If not, we'll do a second sweep, and look for the class name.
        foreach ($this->fixtures as $fixture)
        {
            if ($fixture->name == $name)
            {
                return $fixture;
            }
        }

        throw new TestDummyException(
            'Could not locate a factory with the name: ' . $name
        );
    }

    /**
     * Build up an entity and populate it with dummy data.
     *
     * @param string $name
     * @param array $fields
     * @return array
     */
    public function build($name, $fields = [])
    {
        $data = $this->mergeFixtureWithOverrides($name, $fields);

        // We'll pass off the process of creating the entity.
        // That way, folks can use different persistence layers.
        return $this->database->build($this->getFixture($name)->name, $data);
    }

    /**
     * Build and persist a named entity.
     *
     * @param string $name
     * @param array $fields
     * @return mixed
     */
    public function create($name, array $fields = [])
    {
        $entities = array_map(function() use($name, $fields)
        {
            return $this->persist($name, $fields);
        }, range(1, $this->getTimes()));

        return count($entities) > 1 ? new Collection($entities) : $entities[0];
    }

    /**
     * Merge the fixture with any potential overrides.
     *
     * @param $name
     * @param $fields
     * @return array
     */
    protected function mergeFixtureWithOverrides($name, array $fields)
    {
        $attributes = $this->getFixture($name)->attributes;

        return array_intersect_key($fields, $attributes) + $attributes;
    }

    /**
     * Persist the entity and any relationships.
     *
     * @param string $name
     * @param array $fields
     * @return mixed
     */
    protected function persist($name, array $fields = [])
    {
        $entity = $this->build($name, $fields);

        // We'll filter through all of the columns, and check
        // to see if there are any defined relationships. If there
        // are, then we'll need to create those records as well.
        foreach ($entity->getAttributes() as $columnName => $value)
        {
            if ($relationship = $this->hasRelationshipAttribute($columnName, $value))
            {
                $entity[$columnName] = $this->fetchRelationship($relationship);
            }
        }

        $this->database->save($entity);

        return $entity;
    }

    /**
     * Check if the attribute refers to a relationship.
     *
     * @param $column
     * @param $value
     * @return mixed
     */
    protected function hasRelationshipAttribute($column, $value)
    {
        if (preg_match('/^factory:(.+)$/i', $value, $matches))
        {
            return $matches[1];
        }

        return false;
    }

    /**
     * Get the ID for the relationship.
     *
     * @param $relationshipType
     * @return integer
     */
    protected function fetchRelationship($relationshipType)
    {
        if ($this->isRelationshipAlreadyCreated($relationshipType))
        {
            return $this->relationshipIds[$relationshipType];
        }

        return $this->relationshipIds[$relationshipType] = $this->persist($relationshipType)->id;
    }

    /**
     * Determine if the provided relationship type has already been persisted.
     *
     * @param $relationshipType
     * @return bool
     */
    protected function isRelationshipAlreadyCreated($relationshipType)
    {
        return isset($this->relationshipIds[$relationshipType]);
    }

}
