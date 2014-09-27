<?php namespace Laracasts\TestDummy;

use Symfony\Component\Yaml\Yaml;

class Builder {

	/**
	 * All user-defined fixtures.
	 *
	 * @var array
	 */
	protected $fixtures;

	/**
	 * Number of times to create the record.
	 *
	 * @var int
	 */
	protected $times = 1;

	/**
	 * Cached relationship ids
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
	 * Create test records for testing
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
	 * Get number of times to create a record
	 *
	 * @return int
	 */
	protected function getTimes()
	{
		return $this->times;
	}

	/**
	 * Set number of times to create records
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
	 * @param string $type
	 * @throws TestDummyException
	 * @return
	 */
	public function getFixture($type)
	{
		if ( ! array_key_exists($type, $this->fixtures))
		{
			throw new TestDummyException("You need to create a '{$type}' fixture in your fixtures.yml file.");
		}

		return $this->fixtures[$type];
	}

	/**
	 * Create entity and populate with dummy data.
	 *
	 * @param string $type
	 * @param array  $fields
	 * @return array
	 */
	public function build($type, $fields = [])
	{
		$data = $this->mergeFixtureWithOverrides($type, $fields);

		// We'll pass off the process of creating the entity.
		// This way, folks can use different persistence layers.
		return $this->database->build($type, $data);
	}

	/**
	 * Make and persist an entity.
	 *
	 * @param string $type
	 * @param array  $fields
	 * @return mixed
	 */
	public function create($type, array $fields = [])
	{
		$times = $this->getTimes();
        	$entities = [];

		while ($times--)
		{
			$entities[] = $this->persist($type, $fields);
		}

		return count($entities) > 1 ? $entities : $entities[0];
	}

	/**
	 * Merge the fixture with any potential overrides.
	 *
	 * @param $type
	 * @param $fields
	 * @return array
	 */
	protected function mergeFixtureWithOverrides($type, array $fields)
	{
		// First, we'll merge the default fixture will any
		// overrides that the caller provides.
		$data = array_merge($this->getFixture($type), $fields);

		// Next, we'll do any necessary dynamic replacements.
		return (new DynamicAttributeReplacer)->replace($data);
	}

	/**
	 * Persist the entity and any relationships.
	 *
	 * @param string $type
	 * @param array  $fields
	 * @return mixed
	 */
	protected function persist($type, array $fields = [])
	{
		$entity = $this->build($type, $fields);

		// We'll filter through all of the columns, and check
		// to see if there are any defined relationships. If there
		// are, then we'll need to create those records as well
		foreach ($entity->getAttributes() as $column => $value)
		{
			if ($this->hasRelationshipAttribute($column, $value))
			{
				$entity[$column] = $this->fetchRelationshipId($column, $value['type']);
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
	 * @return boolean
	 */
	protected function hasRelationshipAttribute($column, $value)
	{
		return preg_match('/.+?_id$/', $column) and is_array($value);
	}

	/**
	 * Get the ID for the relationship.
	 *
	 * @param $type
	 * @return integer
	 */
	protected function fetchRelationshipId($column, $type)
	{
		if ($this->isRelationshipAlreadyCreated($column))
		{
			return $this->relationshipIds[$column];
		}

		return $this->relationshipIds[$column] = $this->persist($type)->id;
	}

	/**
	 * Determine if the provided relationship type
	 * has already been persisted.
	 *
	 * @param $relationshipType
	 * @return bool
	 */
	protected function isRelationshipAlreadyCreated($relationshipType)
	{
		return isset($this->relationshipIds[$relationshipType]);
	}

}
