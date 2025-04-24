<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Provider;

use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use PHPUnit\Framework\TestCase;

class ContextProviderTest extends TestCase
{
    private const ENTITY_FQCN = 'App\Domain\Catalog\Entity\Category';

    private const PROJECT_DIR = 'myproject/src';

    private const BASE_NAMESPACE = 'App';

    private const CONTROLLER_TPL = 'App\UI\Controller\{domain}\{entity}';

    private const DTO_TPL = 'App\Application\Dto\{domain}\{entity}';

    private const SERVICE_TPL = 'App\Application\Service\{domain}\{entity}Service';

    private const ERROR_CLASS = 'App\Shared\ErrorDto';

    private const PAGEN_CLASS = 'App\Shared\PaginationDto';

    public function testGetDtoPath(): void
    {
        $provider = $this->createProvider();
        $expectedPath = self::PROJECT_DIR . '/Application/Dto/Domain/Category';
        self::assertSame($expectedPath, $provider->getDtoPath(self::ENTITY_FQCN));
    }

    public function testGetControllerPath(): void
    {
        $provider = $this->createProvider();
        $expectedPath = self::PROJECT_DIR . '/UI/Controller/Domain/Category';
        self::assertSame($expectedPath, $provider->getControllerPath(self::ENTITY_FQCN));
    }

    public function testGetDtoNamespace(): void
    {
        $provider = $this->createProvider();
        $expectedNamespace = 'App\Application\Dto\Domain\Category';
        self::assertSame($expectedNamespace, $provider->getDtoNamespace(self::ENTITY_FQCN));
    }

    public function testGetControllerNamespace(): void
    {
        $provider = $this->createProvider();
        $expectedNamespace = 'App\UI\Controller\Domain\Category';
        self::assertSame($expectedNamespace, $provider->getControllerNamespace(self::ENTITY_FQCN));
    }

    public function testGetters(): void
    {
        $provider = $this->createProvider();
        self::assertSame(['App\BaseController'], $provider->getExtendedClasses());
        self::assertSame(self::ERROR_CLASS, $provider->getErrorResponseClass());
        self::assertSame(self::PAGEN_CLASS, $provider->getPaginationClass());
        self::assertSame(self::PROJECT_DIR, $provider->projectDir);
        self::assertSame(self::BASE_NAMESPACE, $provider->projectBaseNamespace);
        self::assertSame(['App\DtoInterface'], $provider->dtoInterfaces);
        self::assertSame('App\SpecificDto', $provider->dtoFullyQualifiedClassName);
    }

    private function createProvider(): ContextProvider
    {
        return new ContextProvider(
            controllerNamespaceTemplate: self::CONTROLLER_TPL,
            dtoNamespaceTemplate: self::DTO_TPL,
            serviceNamespaceTemplate: self::SERVICE_TPL,
            projectDir: self::PROJECT_DIR,
            projectBaseNamespace: self::BASE_NAMESPACE,
            errorResponseClass: self::ERROR_CLASS,
            paginationClass: self::PAGEN_CLASS,
            extendedClasses: ['App\BaseController'],
            dtoInterfaces: ['App\DtoInterface'],
            dtoFullyQualifiedClassName: 'App\SpecificDto',
        );
    }
}
