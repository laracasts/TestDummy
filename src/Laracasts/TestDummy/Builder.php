<?php namespace Laracasts\TestDummy;

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
    protected $relationshipIds = [];

    /**
     * The buildable repository layer.
     *
     * @var BuildableRepositoryInterface
     */
    private $database;

    /**
     * The faker generator.
     *
     * @var mixed
     */
    private $generator;

    /**
     * Create a new Builder instance.
     *
     * @param BuildableRepositoryInterface $database
     * @param array                        $fixtures
     * @param \Faker\Factory               $generator
     */
    public function __construct(BuildableRepositoryInterface $database, array $fixtures, $generator)
    {
        $this->database = $database;
        $this->fixtures = $fixtures;
        $this->generator = $generator;
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
        return $this->database->build($this->getFixture($name)->name, $data);
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
        $entities = array_map(function () use ($name, $attributes) {
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

        if (is_callable($factory)) {
            $factory = $factory($this->generator, $attributes);

            if ( ! is_array($factory)) {
                throw new TestDummyException("Factory [$name] closure must return an array of attributes.");
            }
        }

        $factory = $this->triggerFakerOnAttributes($factory);

        return array_merge($factory, $attributes);
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
        $databaseAttributes = $this->database->getAttributes($entity);

        // We'll filter through all of the columns, and check
        // to see if there are any defined relationships. If there
        // are, then we'll need to create those records as well.
        foreach ($databaseAttributes as $columnName => $value) {
            if ($relationship = $this->hasRelationshipAttribute($value)) {
                $entity[$columnName] = $this->fetchRelationship($relationship, $attributes);
            }
        }

        $this->database->save($entity);

        return $entity;
    }

    /**
     * Check if the attribute refers to a relationship.
     *
     * @param  string $value
     * @return mixed
     */
    protected function hasRelationshipAttribute($value)
    {
        if (is_string($value) && preg_match('/^factory:(.+)$/i', $value, $matches)) {
            return $matches[1];
        }

        return false;
    }

    /**
     * Get the ID for the relationship.
     *
     * @param  string $relationshipType
     * @return integer
     */
    protected function fetchRelationship($relationshipType, $attributes)
    {
        if ($this->isRelationshipAlreadyCreated($relationshipType)) {
            return $this->relationshipIds[$relationshipType];
        }

        return $this->relationshipIds[$relationshipType] = $this->persist($relationshipType, $attributes)->getKey();
    }

    /**
     * Determine if the provided relationship type has already been persisted.
     *
     * @param  string $relationshipType
     * @return bool
     */
    protected function isRelationshipAlreadyCreated($relationshipType)
    {
        return isset($this->relationshipIds[$relationshipType]);
    }

}
