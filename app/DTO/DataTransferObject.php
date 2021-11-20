<?php

namespace App\DTO;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;

class DataTransferObject
{
    public function __construct(array $parameters = [])
    {
        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            if (isset($parameters[$property])) {
                $this->{$property} = $parameters[$property];

                continue;
            }
            if (isset($parameters[Str::snake($property)])) {
                $this->{$property} = $parameters[Str::snake($property)];

                continue;
            }
        }
    }

    public function has(string $field): bool
    {
        return property_exists($this, $field)
            && isset($this->{$field})
            && !empty($this->{$field});
    }
}
