<?php

namespace JtcSolutions\CodeGenerator\Tests\Integration\CodeRenderer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\IMethodAttributeConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\OpenApiDoc\IOpenApiDocConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\ControllerCodeRenderer;
use PHPUnit\Framework\TestCase;

class ControllerCodeRendererTest extends TestCase
{
    private ControllerCodeRenderer $renderer;
    private ControllerConfiguration $configuration;

    protected function setUp(): void
    {
        $methodAttribute = $this->createMock(IMethodAttributeConfiguration::class);
        $methodAttribute->method('render')->willReturn('#[Route("/test")]');

        $methodArg = new MethodArgumentConfiguration('testArg', 'string');

        $methodConfig = new MethodConfiguration(
            'testMethod',
            'JsonResponse',
            'return $this->json([]);',
            [$methodArg],
            [$methodAttribute]
        );

        $openApiDoc = $this->createMock(IOpenApiDocConfiguration::class);
        $openApiDoc->method('render')->willReturn('#[OA\Tag(name: "Test")]');

        $this->configuration = new ControllerConfiguration(
            'TestController',
            'App\Test\Controller',
            $methodConfig,
            ['BaseController'],
            ['App\Base\BaseController', 'Symfony\Component\HttpFoundation\JsonResponse'],
            [$openApiDoc],
            ['TestInterface'],
            [new MethodArgumentConfiguration('service', 'TestService')],
            'parent::__construct();'
        );

        $this->renderer = new ControllerCodeRenderer($this->configuration);
    }

    public function testGenerateCodeProducesCompleteControllerClass(): void
    {
        $result = $this->renderer->generateCode();

        // Check structure and key elements
        $this->assertStringContainsString('<?php declare(strict_types = 1);', $result);
        $this->assertStringContainsString('namespace App\Test\Controller;', $result);
        $this->assertStringContainsString('use App\Base\BaseController;', $result);
        $this->assertStringContainsString('use Symfony\Component\HttpFoundation\JsonResponse;', $result);
        $this->assertStringContainsString('#[OA\Tag(name: "Test")]', $result);
        $this->assertStringContainsString('class TestController extends BaseController implements TestInterface', $result);
        $this->assertStringContainsString('public function __construct(', $result);
        $this->assertStringContainsString('TestService $service', $result);
        $this->assertStringContainsString('parent::__construct();', $result);
        $this->assertStringContainsString('#[Route("/test")]', $result);
        $this->assertStringContainsString('public function testMethod(', $result);
        $this->assertStringContainsString('string $testArg', $result);
        $this->assertStringContainsString('): JsonResponse {', $result);
        $this->assertStringContainsString('return $this->json([]);', $result);
        $this->assertStringContainsString('}', $result);
    }

    public function testMinimalConfigurationRendering(): void
    {
        $methodConfig = new MethodConfiguration(
            'simple',
            'void',
            'return;',
            [],
            []
        );

        $minimalConfig = new ControllerConfiguration(
            'SimpleController',
            'App\Simple',
            $methodConfig,
            [],
            [],
            [],
            [],
            [],
            null
        );

        $renderer = new ControllerCodeRenderer($minimalConfig);
        $result = $renderer->generateCode();

        $this->assertStringContainsString('<?php declare(strict_types = 1);', $result);
        $this->assertStringContainsString('namespace App\Simple;', $result);
        $this->assertStringContainsString('class SimpleController', $result);
        $this->assertStringNotContainsString('extends', $result);
        $this->assertStringNotContainsString('implements', $result);
        $this->assertStringContainsString('public function simple(', $result);
        $this->assertStringContainsString('): void {', $result);
        $this->assertStringContainsString('return;', $result);
    }
}