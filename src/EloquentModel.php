<?php

namespace Laracasts\TestDummy;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EloquentModel implements IsPersistable
{

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
     * Get a random entity and fill any override attributes
     * @param $type
     * @param array $attributes
     * @param array $existingKeys
     * @return mixed
     * @throws TestDummyException
     */
    public function random($type, array $attributes, array $existingKeys)
    {
        if ( ! class_exists($type)) {
            throw new TestDummyException("The {$type} model was not found.");
        }

        $model = $this->getRandom($type, $existingKeys);

        Eloquent::unguard();
        $model->fill($attributes);
        Eloquent::reguard();

        return $model;
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

        $object = (new $type)->fill($attributes);

        Eloquent::reguard();

        return $object;
    }

    /**
     * Fetch a random entity from the database which is not already in use
     * If none exists, create a new one
     *
     * @param $type
     * @param array $existingKeys
     * @return mixed
     */
    private function getRandom($type, array $existingKeys)
    {
        $object = new $type;
        $count = $type::count() - count($existingKeys);
        if ($count > 0) {
            $rand = mt_rand(0,$count-1);
            return $object->whereNotIn($object->getKeyName(), $existingKeys)->get()[$rand];
        }
        return $object;
    }

}
