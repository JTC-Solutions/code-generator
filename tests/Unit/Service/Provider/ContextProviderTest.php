<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Provider;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider\ContextProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

class ContextProviderTest extends TestCase
{
    private ContextProvider $contextProvider;

    private string $entityFqcn = 'App\Domain\User\Entity\User';

    private string $controllerNamespace = 'App\Domain\User\Controller';

    private string $dtoNamespace = 'App\Domain\User\Dto';

    protected function setUp(): void
    {
        // Note: This relies on the static methods in ContextProvider
        // If getBaseNamespace/getProjectDir need to be mocked, it's more complex.
        $this->contextProvider = new ContextProvider(
            $this->controllerNamespace,
            $this->dtoNamespace,
            $this->entityFqcn,
            Throwable::class,
        );
    }

    public function testGetContext(): void
    {
        $context = $this->contextProvider->getContext();

        self::assertInstanceOf(Context::class, $context);
        self::assertSame($this->entityFqcn, $context->entityFQCN);
        self::assertSame('App\Domain\User\Entity', $context->entityNamespace);
        self::assertSame('project/Domain/User/Entity', $context->entityPath); // Assumes DIRECTORY_SEPARATOR = /
        self::assertSame($this->controllerNamespace, $context->controllerNamespace);
        self::assertSame('project/Domain/User/Controller', $context->controllerPath); // Assumes DIRECTORY_SEPARATOR = /
        self::assertSame($this->dtoNamespace, $context->dtoNamespace);
        self::assertSame('project/Domain/User/Dto', $context->dtoPath); // Assumes DIRECTORY_SEPARATOR = /
        self::assertIsArray($context->defaultUseStatements);
    }

    /**
     * @dataProvider namespaceToPathDataProvider
     */
    public function testNamespaceToPath(string $namespace, string $expectedPath): void
    {
        // Assumes DIRECTORY_SEPARATOR = / for simplicity
        self::assertEquals($expectedPath, ContextProvider::namespaceToPath($namespace));
    }

    public static function namespaceToPathDataProvider(): array
    {
        // Assuming baseNamespace = 'App', projectDir = 'project'
        return [
            'App namespace' => ['App\Service\Util', 'project/Service/Util'],
            'Sub-App namespace' => ['App\Domain\Core\Entity', 'project/Domain/Core/Entity'],
            'No App prefix (treated as relative)' => ['Vendor\Library\Component', 'project/Vendor/Library/Component'],
            'Global namespace' => ['GlobalClass', 'project/GlobalClass'],
            'Trailing slash namespace' => ['App\Trailing\\', 'project/Trailing'],
        ];
    }

    /**
     * @dataProvider pathToNamespaceDataProvider
     */
    public function testPathToNamespace(string $path, string $expectedNamespace): void
    {
        // Assumes DIRECTORY_SEPARATOR = / for simplicity
        self::assertEquals($expectedNamespace, ContextProvider::pathToNamespace($path));
    }

    public static function pathToNamespaceDataProvider(): array
    {
        // Assuming baseNamespace = 'App', projectDir = 'project'
        return [
            'Simple path' => ['project/Service/Util', 'App\Service\Util'],
            'Deeper path' => ['project/Domain/Core/Entity', 'App\Domain\Core\Entity'],
            'Path outside project dir (becomes relative)' => ['other/Vendor/Lib', 'App\other\Vendor\Lib'], // Behaviour might depend on exact logic if prefix check fails
            'Windows path separators' => ['project\Domain\Win', 'App\Domain\Win'],
            'Mixed path separators' => ['project/Mixed\Path', 'App\Mixed\Path'],
            'Path with file' => ['project/Service/MyClass.php', 'App\Service\MyClass.php'],
        ];
    }

    /**
     * @dataProvider fqcnToNamespaceDataProvider
     */
    public function testGetNamespaceFromFQCN(string $fqcn, string $expectedNamespace): void
    {
        self::assertEquals($expectedNamespace, $this->contextProvider->getNamespaceFromFQCN($fqcn));
    }

    public static function fqcnToNamespaceDataProvider(): array
    {
        return [
            'Standard FQCN' => ['App\Domain\User\Entity\User', 'App\Domain\User\Entity'],
            'Vendor FQCN' => ['Vendor\Package\Service', 'Vendor\Package'],
            'No namespace (global)' => ['MyGlobalClass', ''],
            'Leading backslash' => ['\App\Domain\Test', 'App\Domain'], // Assuming trim logic handles this
        ];
    }
}
