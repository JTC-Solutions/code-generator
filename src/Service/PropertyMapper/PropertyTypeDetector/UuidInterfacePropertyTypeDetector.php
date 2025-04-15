<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector;

use JtcSolutions\CodeGenerator\Service\PropertyMapper\IPropertyTypeDetector;
use Ramsey\Uuid\UuidInterface;
use ReflectionNamedType;
use ReflectionProperty;

final class UuidInterfacePropertyTypeDetector implements IPropertyTypeDetector
{
    public function detect(ReflectionProperty $property, ReflectionNamedType $type): string
    {
        return UuidInterface::class;
    }

    public function supports(ReflectionProperty $property, ReflectionNamedType $type): bool
    {
        return $type->getName() === UuidInterface::class;
    }
}
