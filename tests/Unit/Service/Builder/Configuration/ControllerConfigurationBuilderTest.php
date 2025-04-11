<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\BaseController; // Example Base class
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse; // Example dependency

class ControllerConfigurationBuilderTest extends TestCase
{
    private ControllerConfigurationBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new ControllerConfigurationBuilder(
            className: 'TestController',
            namespace: 'App\Controller\Test',
            // MethodConfiguration can be null or mocked if needed for specific tests
        );
    }

    public function testBuildEmpty(): void
    {
        $config = $this->builder->build();
        self::assertSame('TestController', $config->className);
        self::assertSame('App\Controller\Test', $config->namespace);
        self::assertEmpty($config->useStatements);
        self::assertEmpty($config->extends);
        self::assertEmpty($config->interfaces);
        self::assertEmpty($config->openApiDocs);
        self::assertEmpty($config->constructorParams);
        self::assertNull($config->constructorBody);
    }

    public function testAddUseStatement(): void
    {
        $this->builder->addUseStatement(JsonResponse::class); //
        $config = $this->builder->build();
        self::assertCount(1, $config->useStatements);

        foreach ($config->useStatements as $statement) {
            self::assertTrue($statement->fqcn === JsonResponse::class);
        }
    }

    public function testAddUseStatementDuplicateThrowsException(): void
    {
        $this->expectException(ConfigurationException::class); //
        $this->expectExceptionMessageMatches('/Attempted to add useStatements which already is set/');

        $this->builder->addUseStatement(JsonResponse::class); //
        $this->builder->addUseStatement(JsonResponse::class); // Add again
    }

    public function testAddExtendedClass(): void
    {
        $this->builder->addExtendedClass(BaseController::class); //
        $config = $this->builder->build();

        // BaseController should be added to use statements automatically
        foreach ($config->useStatements as $statement) {
            self::assertTrue($statement->fqcn === BaseController::class);
        }
        self::assertCount(1, $config->extends);
        self::assertContains('BaseController', $config->extends); // Class name only
    }

    public function testAddConstructorParam(): void
    {
        $param = new MethodArgumentConfiguration('entityManager', 'EntityManagerInterface'); //
        $this->builder->addConstructorParam($param);
        $config = $this->builder->build();
        self::assertCount(1, $config->constructorParams);
        self::assertSame($param, $config->constructorParams[0]); // Assuming auto-incrementing key
    }
}
