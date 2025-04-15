<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto\DtoCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Dto\DtoConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\ControllerClassWriter;
use JtcSolutions\CodeGenerator\Service\Writer\DtoClassWriter;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

abstract class BaseFunctionalTest extends TestCase
{
    // test configuration
    protected const string DEFAULT_CLASS_FQCN = 'App\DomainName\Domain\Entity\EntityName';

    protected const string CONTROLLER_NAMESPACE_TEMPLATE = 'App\%s\App\Api\%s';

    protected const string DTO_NAMESPACE_TEMPLATE = 'App\%s\Domain\Dto\%s';

    protected const string PROJECT_DIR = 'output';

    private ?ContextProvider $contextProvider = null;

    protected function tearDown(): void
    {
        $this->contextProvider = null;

        // delete all generated files
        $fs = new Filesystem();
        $fs->remove(__DIR__ . '/../../output');
    }

    protected function createDtoGenerator(): DtoGenerator
    {
        return new DtoGenerator(
            classWriter: $this->createDtoClassWriter(),
            configurator: $this->createDtoConfigurator(),
            codeRenderer: $this->createDtoCodeRenderer(),
        );
    }

    protected function createDtoClassWriter(): DtoClassWriter
    {
        return new DtoClassWriter(
            contextProvider: $this->createContextProvider(),
            parserFactory: new ParserFactory(),
        );
    }

    protected function createContextProvider(): ContextProvider
    {
        if ($this->contextProvider === null) {
            $this->contextProvider = new ContextProvider(
                controllerNamespaceTemplate: static::CONTROLLER_NAMESPACE_TEMPLATE,
                dtoNamespaceTemplate: static::DTO_NAMESPACE_TEMPLATE,
                projectDir: static::PROJECT_DIR,
                projectBaseNamespace: 'App',
                errorResponseClass: Throwable::class,
                extendedClasses: [],
                dtoInterfaces: [],
            );
        }

        return $this->contextProvider;
    }

    protected function createDtoConfigurator(): DtoConfigurator
    {
        return new DtoConfigurator(
            contextProvider: $this->createContextProvider(),
        );
    }

    protected function createDtoCodeRenderer(): DtoCodeRenderer
    {
        return new DtoCodeRenderer();
    }

    protected function createControllerCodeRenderer(): ControllerCodeRenderer
    {
        return new ControllerCodeRenderer();
    }

    protected function createControllerClassWriter(): ControllerClassWriter
    {
        return new ControllerClassWriter(
            contextProvider: $this->createContextProvider(),
            parserFactory: new ParserFactory(),
        );
    }
}
