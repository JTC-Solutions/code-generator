<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector;

use JtcSolutions\CodeGenerator\Service\PropertyMapper\IPropertyTypeDetector;
use ReflectionNamedType;
use ReflectionProperty;

final class EntityPropertyTypeDetector implements IPropertyTypeDetector
{
    /**
     * @param class-string $entityInterface
     * @param class-string $replacementClass
     */
    public function __construct(
        protected readonly string $entityInterface,
        protected readonly string $replacementClass,
    ) {
    }

    public function detect(ReflectionProperty $property, ReflectionNamedType $type): string
    {
        return $this->replacementClass;
    }

    public function supports(ReflectionProperty $property, ReflectionNamedType $type): bool
    {
        return is_subclass_of($type->getName(), $this->entityInterface);
    }
}
