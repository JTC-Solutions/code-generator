<?php declare(strict_types = 1);

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
            [$methodAttribute],
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
            'parent::__construct();',
        );

        $this->renderer = new ControllerCodeRenderer($this->configuration);
    }

    public function testGenerateCodeProducesCompleteControllerClass(): void
    {
        $result = $this->renderer->generateCode();

        // Check structure and key elements
        self::assertStringContainsString('<?php declare(strict_types = 1);', $result);
        self::assertStringContainsString('namespace App\Test\Controller;', $result);
        self::assertStringContainsString('use App\Base\BaseController;', $result);
        self::assertStringContainsString('use Symfony\Component\HttpFoundation\JsonResponse;', $result);
        self::assertStringContainsString('#[OA\Tag(name: "Test")]', $result);
        self::assertStringContainsString('class TestController extends BaseController implements TestInterface', $result);
        self::assertStringContainsString('public function __construct(', $result);
        self::assertStringContainsString('TestService $service', $result);
        self::assertStringContainsString('parent::__construct();', $result);
        self::assertStringContainsString('#[Route("/test")]', $result);
        self::assertStringContainsString('public function testMethod(', $result);
        self::assertStringContainsString('string $testArg', $result);
        self::assertStringContainsString('): JsonResponse {', $result);
        self::assertStringContainsString('return $this->json([]);', $result);
        self::assertStringContainsString('}', $result);
    }

    public function testMinimalConfigurationRendering(): void
    {
        $methodConfig = new MethodConfiguration(
            'simple',
            'void',
            'return;',
            [],
            [],
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
            null,
        );

        $renderer = new ControllerCodeRenderer($minimalConfig);
        $result = $renderer->generateCode();

        self::assertStringContainsString('<?php declare(strict_types = 1);', $result);
        self::assertStringContainsString('namespace App\Simple;', $result);
        self::assertStringContainsString('class SimpleController', $result);
        self::assertStringNotContainsString('extends', $result);
        self::assertStringNotContainsString('implements', $result);
        self::assertStringContainsString('public function simple(', $result);
        self::assertStringContainsString('): void {', $result);
        self::assertStringContainsString('return;', $result);
    }
}
