<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector;

use DateTimeImmutable;
use DateTimeInterface;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\IPropertyTypeDetector;
use ReflectionNamedType;
use ReflectionProperty;

final class DateTimePropertyTypeDetector implements IPropertyTypeDetector
{
    private const array SUPPORTED_TYPES = [
        DateTimeInterface::class,
        DateTimeImmutable::class,
    ];

    public function detect(ReflectionProperty $property, ReflectionNamedType $type): string
    {
        return DateTimeImmutable::class;
    }

    public function supports(ReflectionProperty $property, ReflectionNamedType $type): bool
    {
        return in_array($type->getName(), self::SUPPORTED_TYPES, true);
    }
}
