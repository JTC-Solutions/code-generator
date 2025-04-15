<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Integration\Service\Generator\Dto;

use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Throwable;

class DtoGeneratorTest extends TestCase
{
    private vfsStreamDirectory $root;

    private Context $context;

    private DtoGenerator $generator;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('projectDir');
        $this->generator = new DtoGenerator();

        $this->context = new Context(
            entityFQCN: 'App\Entity\Product',
            entityNamespace: 'App\Entity',
            entityPath: 'vfs://projectDir/Entity',
            controllerPath: 'vfs://projectDir/Controller',
            controllerNamespace: 'App\Controller',
            dtoPath: 'vfs://projectDir/Dto/Product', // Specific DTO path
            dtoNamespace: 'App\Dto\Product', // Specific DTO namespace
            errorResponseClass: Throwable::class,
        );

        // Ensure DTO directory exists
        vfsStream::newDirectory('Dto/Product')->at($this->root);
    }

    public function testGenerateDefaultDto(): void
    {
        $dtoSuffix = 'Data';
        $expectedClassName = 'Product' . $dtoSuffix; // Product + Suffix
        $expectedFilePath = 'vfs://projectDir/Dto/Product/' . $expectedClassName . '.php';

        $resultPath = $this->generator->generate($this->context, '', $dtoSuffix); // No prefix, specific suffix

        self::assertEquals($expectedFilePath, $resultPath);
        self::assertTrue($this->root->hasChild('Dto/Product/' . $expectedClassName . '.php'));

        $generatedCode = file_get_contents($resultPath);
        self::assertStringContainsString('<?php declare(strict_types = 1);', $generatedCode);
        self::assertStringContainsString('namespace App\Dto\Product;', $generatedCode);
        self::assertStringContainsString('readonly class ' . $expectedClassName, $generatedCode);
        self::assertStringContainsString('{', $generatedCode);
        self::assertStringContainsString('// TODO: Add properties', $generatedCode); // Placeholder from renderer
        self::assertStringContainsString('}', $generatedCode);
    }

    public function testGenerateWithPrefixAndSuffix(): void
    {
        $dtoPrefix = 'ApiRequest';
        $dtoSuffix = 'Input';
        $expectedClassName = $dtoPrefix . 'Product' . $dtoSuffix; // Prefix + Product + Suffix
        $expectedFilePath = 'vfs://projectDir/Dto/Product/' . $expectedClassName . '.php';

        $resultPath = $this->generator->generate($this->context, $dtoPrefix, $dtoSuffix);

        self::assertEquals($expectedFilePath, $resultPath);
        self::assertTrue($this->root->hasChild('Dto/Product/' . $expectedClassName . '.php'));
        $generatedCode = file_get_contents($resultPath);
        self::assertStringContainsString('readonly class ' . $expectedClassName, $generatedCode);
    }
}
