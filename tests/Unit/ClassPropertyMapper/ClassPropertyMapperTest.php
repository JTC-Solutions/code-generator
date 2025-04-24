<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\ClassPropertyMapper;

use DateTimeImmutable;
use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\ClassPropertyMapper;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\DateTimePropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\EntityPropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\UuidInterfacePropertyTypeDetector;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\EntityId;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\EntityInterface;
use JtcSolutions\CodeGenerator\Tests\Unit\ClassPropertyMapper\TestClasses\ChildClass;
use JtcSolutions\CodeGenerator\Tests\Unit\ClassPropertyMapper\TestClasses\ComplexClass;
use JtcSolutions\CodeGenerator\Tests\Unit\ClassPropertyMapper\TestClasses\SimpleClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

class ClassPropertyMapperTest extends TestCase
{
    protected ClassPropertyMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $propertyTypeDetectors = [
            new DateTimePropertyTypeDetector(),
            new UuidInterfacePropertyTypeDetector(),
            new EntityPropertyTypeDetector(EntityInterface::class, EntityId::class),
        ];

        $this->mapper = new ClassPropertyMapper($propertyTypeDetectors);
    }

    public function testSimpleClassProperties(): void
    {
        $expectedMap = [
            new MappedProperty('name', 'string'),
            new MappedProperty('count', 'int'),
            new MappedProperty('price', 'float'),
            new MappedProperty('isActive', 'bool'),
            new MappedProperty('items', 'array'),
        ];

        $actualMap = $this->mapper->getPropertyMap(SimpleClass::class);

        self::assertEquals($expectedMap, $actualMap);
    }

    public function testComplexClassProperties(): void
    {
        $expectedMap = [
            new MappedProperty('id', 'UuidInterface', UuidInterface::class),
            new MappedProperty('description', 'string', null, true),
            new MappedProperty('createdAt', 'DateTimeImmutable', DateTimeImmutable::class),
            new MappedProperty('updatedAt', 'DateTimeImmutable', DateTimeImmutable::class, true),
            new MappedProperty('relatedObject', 'EntityId', EntityId::class),
            new MappedProperty('optionalRelation', 'mixed'),
            new MappedProperty('untypedVar', 'mixed'),
            new MappedProperty('mixedVar', 'mixed', null, true),
            new MappedProperty('standardObject', 'mixed'),
            new MappedProperty('strings', 'array'),
        ];

        $actualMap = $this->mapper->getPropertyMap(ComplexClass::class);

        // Assert that the generated map matches the expected map
        self::assertEquals($expectedMap, $actualMap);
    }

    public function testInheritedProperties(): void
    {
        $expectedMap = [
            new MappedProperty('childProperty', 'int'),
            new MappedProperty('inheritedProperty', 'string'),
            new MappedProperty('protectedInherited', 'string'),
        ];

        $actualMap = $this->mapper->getPropertyMap(ChildClass::class);

        ksort($expectedMap);
        ksort($actualMap);

        self::assertEquals($expectedMap, $actualMap);
    }

    public function testNonExistentClass(): void
    {
        $nonExistentClass = 'App\\This\\Class\\Does\\Not\\Exist';

        // Expect a ReflectionException to be thrown
        $this->expectException(ReflectionException::class);
        $this->expectExceptionMessage("Class or interface '{$nonExistentClass}' not found.");

        // Attempt to get the map for the non-existent class
        $this->mapper->getPropertyMap($nonExistentClass);
    }
}
