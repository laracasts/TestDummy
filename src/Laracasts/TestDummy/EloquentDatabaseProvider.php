<?php namespace Laracasts\TestDummy;


use Illuminate\Database\Eloquent\Model as Eloquent;

class EloquentDatabaseProvider implements BuildableRepositoryInterface {

    /**
     * Create a new EloquentDatabase Provider instance.
     */
    function __construct()
    {
        // We can disable mass assignment protection.
        Eloquent::unguard();
    }

    /**
     * Build the entity with attributes.
     *
     * @param string $type
     * @param array $attributes
     * @throws TestDummyException
     * @return Eloquent
     */
    public function build($type, array $attributes)
    {
        if ( ! class_exists($type))
        {
            throw new TestDummyException("The {$type} model was not found.");
        }

        $object = new $type($attributes);

        // Re-enable mass assignment protection
        Eloquent::reguard();

        return $object;
    }

    /**
     * Persist the entity.
     *
     * @param Model $entity
     * @return void
     */
    public function save($entity)
    {
        $entity->save();

        // Re-enable mass assignment protection
        Eloquent::reguard();
    }

}
