<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\PropertyMapper;

use ReflectionNamedType;
use ReflectionProperty;

interface IPropertyTypeDetector
{
    /**
     * @return class-string
     */
    public function detect(ReflectionProperty $property, ReflectionNamedType $type): string;

    public function supports(ReflectionProperty $property, ReflectionNamedType $type): bool;
}
