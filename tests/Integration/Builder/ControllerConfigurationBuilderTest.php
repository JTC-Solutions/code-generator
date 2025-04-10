<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Integration\Builder;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\OpenApiDoc\IOpenApiDocConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use PHPUnit\Framework\TestCase;

class ControllerConfigurationBuilderTest extends TestCase
{
    private ControllerConfigurationBuilder $builder;

    private MethodConfiguration $methodConfiguration;

    protected function setUp(): void
    {
        $this->methodConfiguration = new MethodConfiguration(
            'testMethod',
            'void',
            'return;',
            [],
            [],
        );

        $this->builder = new ControllerConfigurationBuilder(
            'TestController',
            'App\Test',
            $this->methodConfiguration,
        );
    }

    public function testBuild(): void
    {
        $result = $this->builder->build();

        self::assertInstanceOf(ControllerConfiguration::class, $result);
        self::assertEquals('TestController', $result->className);
        self::assertEquals('App\Test', $result->namespace);
        self::assertSame($this->methodConfiguration, $result->methodConfiguration);
    }

    public function testAddUseStatement(): void
    {
        $this->builder->addUseStatement('App\Test\TestClass');
        $this->builder->addUseStatement('App\Test\AnotherClass', 5);

        $result = $this->builder->build();

        self::assertContains('App\Test\TestClass', $result->useStatements);
        self::assertContains('App\Test\AnotherClass', $result->useStatements);
    }

    public function testAddDuplicateUseStatementThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $this->builder->addUseStatement('App\Test\TestClass');
        $this->builder->addUseStatement('App\Test\TestClass');
    }

    public function testAddExtendedClass(): void
    {
        $this->builder->addExtendedClass('App\Test\BaseClass');

        $result = $this->builder->build();

        self::assertContains('BaseClass', $result->extends);
        self::assertContains('App\Test\BaseClass', $result->useStatements);
    }

    public function testAddOpenApiDoc(): void
    {
        $mockDoc = $this->createMock(IOpenApiDocConfiguration::class);
        $mockDoc->method('getIdentifier')->willReturn('testDoc');

        $this->builder->addOpenApiDoc($mockDoc);

        $result = $this->builder->build();

        self::assertContains($mockDoc, $result->openApiDocs);
    }

    public function testAddDuplicateOpenApiDocThrowsException(): void
    {
        $this->expectException(ConfigurationException::class);

        $mockDoc = $this->createMock(IOpenApiDocConfiguration::class);
        $mockDoc->method('getIdentifier')->willReturn('testDoc');

        $this->builder->addOpenApiDoc($mockDoc);
        $this->builder->addOpenApiDoc($mockDoc);
    }

    public function testAddInterface(): void
    {
        $this->builder->addInterface('TestInterface');

        $result = $this->builder->build();

        self::assertContains('TestInterface', $result->interfaces);
    }

    public function testAddConstructorParam(): void
    {
        $param = new MethodArgumentConfiguration('testParam', 'string');
        $this->builder->addConstructorParam($param);

        $result = $this->builder->build();

        self::assertContains($param, $result->constructorParams);
    }
}
