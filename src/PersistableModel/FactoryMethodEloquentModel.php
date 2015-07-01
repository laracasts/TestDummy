<?php

namespace Laracasts\TestDummy\PersistableModel;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Laracasts\TestDummy\PersistableModel\IsPersistable;
use Illuminate\Bus\MarshalException;
use Laracasts\TestDummy\PersistableModel\EloquentModel;

class FactoryMethodEloquentModel extends EloquentModel implements IsPersistable
{
    protected static $allowedSaveMethodNames = [
        'create{className}',
        'add',
        'write',
    ];

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
        $entity = new $type();
        $saveMethodName = $this->getSaveMethodName($entity);

        if (null == $saveMethodName) {
            return parent::build($type, $attributes);
        }

        $methodParameters = (new \ReflectionClass($type))->getMethod($saveMethodName)->getParameters();

        $parameters = array_map(function ($parameter) use ($attributes) {
            if (array_key_exists($parameter->name, $attributes)) {
                return $attributes[$parameter->name];
            } else if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
        }, $methodParameters);

        $object = forward_static_call_array([$type, $saveMethodName], $parameters);

        //save additional options if not included in add or write method
        $methodParameters = array_combine(array_map(function ($parameter) {
            return $parameter->getName();
        }, $methodParameters), $parameters);

        foreach ($attributes as $attributeName => $attributeValue) {
            if (!isset($methodParameters[$attributeName])) {
                $object->$attributeName = $attributeValue;
            }
        }

        return $object;
    }

    protected function getSaveMethodName($className)
    {
        $allowedSaveMothodNames = array_map(function ($methodName) use ($className) {
            return str_replace([
                '{className}'
            ], [
                $className
            ], $methodName);
        }, self::$allowedSaveMethodNames);

        $methodsList = get_class_methods($className);
        foreach ($allowedSaveMothodNames as $methodName) {
            if (in_array($methodName, $methodsList)) {
                return $methodName;
            }
        }

        return null;
    }
}
