<?php

namespace JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector;

use JtcSolutions\CodeGenerator\Service\PropertyMapper\IPropertyTypeDetector;
use ReflectionNamedType;
use ReflectionProperty;

class EnumPropertyTypeDetector implements IPropertyTypeDetector
{
    public function detect(ReflectionProperty $property, ReflectionNamedType $type): string
    {
        return $type->getName();
    }

    public function supports(ReflectionProperty $property, ReflectionNamedType $type): bool
    {
        return enum_exists($type->getName());
    }
}
