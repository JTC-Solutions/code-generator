<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoPropertyConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\DtoConfigurationBuilder;
use JtcSolutions\CodeGenerator\Tests\Fixtures\DummyInterface;
use PHPUnit\Framework\TestCase;

class DtoConfigurationBuilderTest extends TestCase
{
    public function testBuildBasic(): void
    {
        $builder = new DtoConfigurationBuilder('MyDto', 'App\Test\Dto');
        $config = $builder->build();

        self::assertInstanceOf(DtoConfiguration::class, $config);
        self::assertSame('MyDto', $config->getClassName());
        self::assertSame('App\Test\Dto', $config->getNamespace());
        self::assertEmpty($config->getProperties());
        self::assertEmpty($config->getUseStatements());
        self::assertEmpty($config->getInterfaces());
        self::assertEmpty($config->getExtends());
    }

    public function testBuildWithProperty(): void
    {
        $builder = new DtoConfigurationBuilder('MyDto', 'App\Test\Dto');
        $builder->addProperty(new MappedProperty('title', 'string'));
        $config = $builder->build();

        self::assertCount(1, $config->getProperties());
        self::assertInstanceOf(DtoPropertyConfiguration::class, $config->getProperties()[0]);
        self::assertSame('title', $config->getProperties()[0]->propertyName);
        self::assertSame('string', $config->getProperties()[0]->propertyType);
    }

    public function testBuildWithPropertyWithUseStatement(): void
    {
        $builder = new DtoConfigurationBuilder('MyDto', 'App\Test\Dto');
        $builder->addProperty(new MappedProperty('dummy', 'DummyInterface', DummyInterface::class));
        $config = $builder->build();

        self::assertCount(1, $config->getProperties());
        self::assertInstanceOf(DtoPropertyConfiguration::class, $config->getProperties()[0]);
        self::assertSame('dummy', $config->getProperties()[0]->propertyName);
        self::assertSame('DummyInterface', $config->getProperties()[0]->propertyType); // Short name

        self::assertCount(1, $config->getUseStatements());
        self::assertInstanceOf(UseStatementConfiguration::class, $config->getUseStatements()[0]);
        self::assertSame(DummyInterface::class, $config->getUseStatements()[0]->fqcn);
        self::assertNull($config->getUseStatements()[0]->alias);
    }

    public function testBuildWithInterface(): void
    {
        $builder = new DtoConfigurationBuilder('MyDto', 'App\Test\Dto');
        $builder->addInterface(DummyInterface::class);
        $config = $builder->build();

        self::assertCount(1, $config->getInterfaces());
        self::assertSame('DummyInterface', $config->getInterfaces()[0]); // Short name
        self::assertCount(1, $config->getUseStatements());
        self::assertSame(DummyInterface::class, $config->getUseStatements()[0]->fqcn);
    }

    public function testBuildWithUseStatementAlias(): void
    {
        $builder = new DtoConfigurationBuilder('MyDto', 'App\Test\Dto');
        $builder->addUseStatement(DummyInterface::class, 'DI');
        $config = $builder->build();

        self::assertCount(1, $config->getUseStatements());
        self::assertSame(DummyInterface::class, $config->getUseStatements()[0]->fqcn);
        self::assertSame('DI', $config->getUseStatements()[0]->alias);
    }

    public function testAddDuplicatePropertyThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Attempted to add property which already is set. Item JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoPropertyConfiguration_name is already set in existing');

        $builder = new DtoConfigurationBuilder('TestDto', 'App\Test\Dto');
        $property = new MappedProperty('name', 'string');
        $builder->addProperty($property);
        $builder->addProperty($property);
    }

    public function testAddDuplicateInterfaceThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Attempted to add interface which already is set. Item DummyInterface is already set in existing');

        $builder = new DtoConfigurationBuilder('TestDto', 'App\Test\Dto');
        $builder->addInterface(DummyInterface::class);
        $builder->addInterface(DummyInterface::class);
    }

    public function testAddPropertyWithOrder(): void
    {
        $builder = new DtoConfigurationBuilder('MyDto', 'App\Test\Dto');
        $builder->addProperty(new MappedProperty('second', 'string'), 1);
        $builder->addProperty(new MappedProperty('first', 'int'), 0);
        $config = $builder->build();

        self::assertCount(2, $config->getProperties());
        self::assertSame('first', $config->getProperties()[0]->propertyName);
        self::assertSame('second', $config->getProperties()[1]->propertyName);
    }

    public function testAddPropertyWithDuplicateOrderThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Attempted to add property with order 0. Item attempted to add JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoPropertyConfiguration_second into existing items');

        $builder = new DtoConfigurationBuilder('MyDto', 'App\Test\Dto');
        $builder->addProperty(new MappedProperty('first', 'int'), 0);
        $builder->addProperty(new MappedProperty('second', 'string'), 0);
    }
}
