<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector;

use JtcSolutions\CodeGenerator\Service\PropertyMapper\IPropertyTypeDetector;
use ReflectionNamedType;
use ReflectionProperty;

class EnumPropertyTypeDetector implements IPropertyTypeDetector
{
    public function detect(ReflectionProperty $property, ReflectionNamedType $type): string
    {
        /** @var class-string $enumFQCN */
        $enumFQCN = $type->getName();

        return $enumFQCN;
    }

    public function supports(ReflectionProperty $property, ReflectionNamedType $type): bool
    {
        return enum_exists($type->getName());
    }
}
