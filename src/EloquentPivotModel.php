<?php

namespace Laracasts\TestDummy;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EloquentPivotModel implements IsPersistable {

    /**
     * @var Eloquent
     */
    protected $parent;

    /**
     * @var string
     */
    protected $table;

    /**
     * Constructor.
     *
     * @param Eloquent $parent
     * @param string   $table
     */
    public function __construct(Eloquent $parent, $table)
    {
        $this->parent = $parent;
        $this->table = $table;
    }

    /**
     * Build the entity with attributes.
     *
     * @param  string $type
     * @param  array  $attributes
     * @throws TestDummyException
     * @return Eloquent
     */
    public function build($type, array $attributes)
    {
        if ( ! class_exists($type)) {
            throw new TestDummyException("The {$type} model was not found.");
        }

        return $this->fill($type, $attributes);
    }

    /**
     * Persist the entity.
     *
     * @param  Model $entity
     * @return void
     */
    public function save($entity)
    {
        $entity->save();
    }

    /**
     * Get all attributes for the model.
     *
     * @param  object $entity
     * @return array
     */
    public function getAttributes($entity)
    {
        return $entity->getAttributes();
    }

    /**
     * Force fill an object with attributes.
     *
     * @param  string $type
     * @param  array  $attributes
     * @return Model
     */
    private function fill($type, $attributes)
    {
        Eloquent::unguard();

        $model = new $type($this->parent, $attributes, $this->table, $exists = false);

        Eloquent::reguard();

        return $model;
    }

}
