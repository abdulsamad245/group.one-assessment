<?php

namespace App\Traits;

use ReflectionClass;
use ReflectionProperty;

trait DTOToArray
{
    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);

        $data = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($this);

            if ($value !== null) {
                $data[$property->getName()] = $value;
            }
        }

        return $data;
    }
}
