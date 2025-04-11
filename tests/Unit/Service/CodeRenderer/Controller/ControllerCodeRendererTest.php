<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\Attribute\RouteAttributeConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\OpenApiDocTagConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\BaseController;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use OpenApi\Attributes\Tag;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ControllerCodeRendererTest extends TestCase
{
    public function testRenderWithUseStatements(): void
    {
        $config = $this->createBasicConfig(
            useStatements: [
                new UseStatementConfiguration(JsonResponse::class),
                new UseStatementConfiguration(UuidInterface::class),
            ],
        );
        $renderer = new ControllerCodeRenderer($config);
        $code = $renderer->renderCode();

        self::assertStringContainsString('use Symfony\Component\HttpFoundation\JsonResponse;', $code);
        self::assertStringContainsString('use Ramsey\Uuid\UuidInterface;', $code);
        self::assertStringContainsString("namespace App\Generated;\n\nuse", $code); // Ensure newline after namespace
    }

    public function testRenderWithExtends(): void
    {
        // Assuming BaseController is imported via use statement added automatically by builder or manually here
        $config = $this->createBasicConfig(
            useStatements: [new UseStatementConfiguration(BaseController::class)],
            extends: ['BaseController'], // Builder adds class name, not FQCN here
        );
        $renderer = new ControllerCodeRenderer($config);
        $code = $renderer->renderCode();

        self::assertStringContainsString('use JtcSolutions\CodeGenerator\CodeGenerator\MoveToOtherPackage\BaseController;', $code);
        self::assertStringContainsString('class TestController extends BaseController', $code);
    }

    public function testRenderWithMethod(): void
    {
        $methodConfig = new MethodConfiguration(
            name: 'myAction',
            returnType: 'JsonResponse',
            methodBody: "return \$this->json(['status' => 'ok']);",
            arguments: [new MethodArgumentConfiguration('id', 'UuidInterface')],
            attributes: [new RouteAttributeConfiguration('/test/{id}', 'test_action', ['GET'])],
        );
        $config = $this->createBasicConfig(
            methodConfig: $methodConfig,
            useStatements: [
                new UseStatementConfiguration(JsonResponse::class),
                new UseStatementConfiguration(UuidInterface::class),
                new UseStatementConfiguration(Route::class),
            ],
        );
        $renderer = new ControllerCodeRenderer($config);
        $code = $renderer->renderCode();

        self::assertStringContainsString("#[Route('/test/{id}', name: 'test_action', methods: ['GET'])]", $code);
        self::assertStringContainsString('public function myAction(', $code);
        self::assertStringContainsString('UuidInterface $id', $code);
        self::assertStringContainsString('): JsonResponse {', $code);
        self::assertStringContainsString("return \$this->json(['status' => 'ok']);", $code);
    }

    public function testRenderWithConstructor(): void
    {
        $config = $this->createBasicConfig(
            useStatements: [
                new UseStatementConfiguration('Psr\Log\LoggerInterface'),
                new UseStatementConfiguration('Doctrine\ORM\EntityManagerInterface'),
            ],
            constructorParams: [
                new MethodArgumentConfiguration('logger', 'LoggerInterface'),
                new MethodArgumentConfiguration('entityManager', 'EntityManagerInterface'),
            ],
            constructorBody: "\$this->logger = \$logger;\n        // More complex body", // Add needed use statements
        );
        $renderer = new ControllerCodeRenderer($config);
        $code = $renderer->renderCode();

        self::assertStringContainsString('public function __construct(', $code);
        self::assertStringContainsString('LoggerInterface $logger', $code);
        self::assertStringContainsString('EntityManagerInterface $entityManager', $code);
        self::assertStringContainsString(") {\n        \$this->logger = \$logger;", $code);
        self::assertStringContainsString('        // More complex body', $code);
        self::assertStringContainsString("    }\n", $code); // Closing brace for constructor
    }

    public function testRenderWithOpenApiDocs(): void
    {
        $config = $this->createBasicConfig(
            useStatements: [new UseStatementConfiguration(Tag::class)],
            openApiDocs: [new OpenApiDocTagConfiguration('TestTag')], // Need OA namespace
        );
        $renderer = new ControllerCodeRenderer($config);
        $code = $renderer->renderCode();

        self::assertStringContainsString("#[OA\Tag(name: 'TestTag')]", $code);
        self::assertStringContainsString("#[OA\Tag(name: 'TestTag')]\nclass TestController", $code); // Position before class
    }

    private function createBasicConfig(
        ?MethodConfiguration $methodConfig = null,
        array $useStatements = [],
        array $extends = [],
        array $openApiDocs = [],
        array $constructorParams = [],
        ?string $constructorBody = null,
    ): ControllerConfiguration {
        return new ControllerConfiguration(
            className: 'TestController',
            namespace: 'App\Generated',
            methodConfiguration: $methodConfig,
            extends: $extends,
            useStatements: $useStatements,
            openApiDocs: $openApiDocs,
            constructorParams: $constructorParams,
            constructorBody: $constructorBody,
        );
    }

    // Add tests for interfaces, combinations of features etc.
}
