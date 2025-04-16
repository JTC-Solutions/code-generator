<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\PropertyMapper;

use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class ClassPropertyMapper
{
    /**
     * @param IPropertyTypeDetector[] $propertyTypeDetectors
     */
    public function __construct(
        #[AutowireIterator('jtc_solutions.property_type_detector')]
        private readonly iterable $propertyTypeDetectors,
    ) {
    }

    /**
     * @return MappedProperty[]
     */
    public function getPropertyMap(string $fqcn): array
    {
        if (! class_exists($fqcn) && ! interface_exists($fqcn)) {
            throw new ReflectionException("Class or interface '{$fqcn}' not found.");
        }

        $reflectionClass = new ReflectionClass($fqcn);
        $properties = $reflectionClass->getProperties();
        $map = [];

        foreach ($properties as $property) {
            $map[] = $this->getPropertyType($property, $property->getType());
        }

        return $map;
    }

    private function getPropertyType(ReflectionProperty $property, ?ReflectionType $propertyType): MappedProperty
    {
        if ($propertyType instanceof ReflectionNamedType) {
            if ($propertyType->isBuiltin() === true) {
                return new MappedProperty($property->getName(), $propertyType->getName());
            }

            foreach ($this->propertyTypeDetectors as $propertyTypeDetector) {
                if ($propertyTypeDetector->supports($property, $propertyType)) {
                    $fullyQualifiedClassName = $propertyTypeDetector->detect($property, $propertyType);
                    return new MappedProperty(
                        name: $property->getName(),
                        type: FQCNHelper::transformFQCNToShortClassName($fullyQualifiedClassName),
                        useStatement: $fullyQualifiedClassName,
                    );
                }
            }
        }

        return new MappedProperty($property->getName(), 'mixed');
    }
}
