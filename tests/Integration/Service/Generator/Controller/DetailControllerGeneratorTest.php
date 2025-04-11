<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Integration\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context; //
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller\DetailControllerGenerator; //
use PHPUnit\Framework\TestCase;

class DetailControllerGeneratorTest extends TestCase
{
    private string $tempDir;

    private Context $context;

    protected function setUp(): void
    {
        // Setup vfsStream or create a real temp directory
        $this->tempDir = sys_get_temp_dir() . '/code_gen_test_' . uniqid();
        mkdir($this->tempDir . '/Controller', 0777, true);
        mkdir($this->tempDir . '/Dto', 0777, true);
        mkdir($this->tempDir . '/Entity', 0777, true); // Assuming entity path needed for context

        // Create a realistic context
        $entityFqcn = 'App\Entity\TestProduct';
        $this->context = new Context(
            entityFQCN: $entityFqcn,
            entityNamespace: 'App\Entity', //
            entityPath: $this->tempDir . '/Entity',
            controllerPath: $this->tempDir . '/Controller', //
            controllerNamespace: 'App\Controller\TestProduct', //
            dtoPath: $this->tempDir . '/Dto', //
            dtoNamespace: 'App\Dto\TestProduct', //
            // Add default use statements if needed
        );
    }

    protected function tearDown(): void
    {
        // Clean up the temporary directory or vfsStream
        // Be careful with recursive deletion!
        if (is_dir($this->tempDir)) {
            // Simple cleanup, consider a more robust solution for real projects
            system('rm -rf ' . escapeshellarg($this->tempDir));
        }
    }

    public function testGenerateCreatesCorrectControllerFile(): void
    {
        $generator = new DetailControllerGenerator();
        $generator->generate($this->context);

        $expectedFilePath = $this->tempDir . '/Controller/DetailTestProductController.php';
        self::assertFileExists($expectedFilePath);

        $generatedCode = file_get_contents($expectedFilePath);
        self::assertStringContainsString('namespace App\Controller\TestProduct;', $generatedCode); //
        self::assertStringContainsString('class DetailTestProductController extends BaseController', $generatedCode); //
        self::assertStringContainsString('use App\Entity\TestProduct;', $generatedCode); //
        self::assertStringContainsString('#[Route(\'/api/v1/test-product/{entity}\', name: \'test_product_detail\', methods: [\'GET\'])]', $generatedCode); //
        self::assertStringContainsString('public function detail(', $generatedCode); //
        self::assertStringContainsString('TestProduct $entity', $generatedCode); // Argument type
        self::assertStringContainsString('): JsonResponse', $generatedCode); // Return type
        self::assertStringContainsString('$this->json($entity, Response::HTTP_OK, [], [\'groups\' => [\'testProduct:detail\', \'reference\']]);', $generatedCode); // Example body content

        // Optional: Use snapshot testing for the entire file content
    }
}
