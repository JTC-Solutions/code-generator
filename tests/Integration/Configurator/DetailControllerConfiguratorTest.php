<?php
declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Integration\Configurator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\DetailControllerConfigurator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DetailControllerConfiguratorTest extends TestCase
{
    private DetailControllerConfigurator $configurator;

    protected function setUp(): void
    {
        $this->configurator = new DetailControllerConfigurator();
    }

    public function testConfigureCreatesValidConfiguration(): void
    {
        $domain = 'TestDomain';
        $entity = 'App\Entity\Test';

        $result = $this->configurator->configure($domain, $entity);

        self::assertInstanceOf(ControllerConfiguration::class, $result, 'Invalid instance provided.');
        self::assertEquals('DetailTestController', $result->className, 'Class name does not match.');
        self::assertEquals('App\TestDomain\App\Api\Test', $result->namespace, 'Namespace does not match.');
        self::assertTrue(in_array('BaseController', $result->extends), 'Extended class is missing.');
        self::assertTrue(in_array(JsonResponse::class, $result->useStatements), 'Use statement is missing.');
        self::assertTrue(in_array(Route::class, $result->useStatements), 'Use statement is missing.');
        self::assertEquals('detail', $result->methodConfiguration->name, 'Method name does not match.');
        self::assertEquals('JsonResponse', $result->methodConfiguration->returnType, 'Method return type does not match.');
        self::assertCount(1, $result->methodConfiguration->arguments, 'Number of method arguments does not match.');
        self::assertEquals('entity', $result->methodConfiguration->arguments[0]->argumentName, 'Method argument name does not match.');
        self::assertEquals('Test', $result->methodConfiguration->arguments[0]->argumentType, 'Method argument type does not match.');
    }

    public function testCreateMethodConfiguration(): void
    {
        $entity = 'App\Entity\TestEntity';

        $result = $this->configurator->createMethodConfiguration($entity);

        self::assertEquals('detail', $result->name, 'Method name does not match.');
        self::assertEquals('JsonResponse', $result->returnType, 'Method return type does not match.');
        self::assertCount(1, $result->arguments, 'Number of method arguments does not match.');
        self::assertEquals('entity', $result->arguments[0]->argumentName, 'Method argument name does not match.');
        self::assertEquals('TestEntity', $result->arguments[0]->argumentType, 'Method argument type does not match.');
        self::assertStringContainsString('$this->checkPermissions(TestEntity::class', $result->methodBody, 'Method body does not contain expected string.');
        self::assertStringContainsString("'groups' => ['testEntity:detail', 'reference']", $result->methodBody, 'Method body does not contain expected string.');
    }
}
