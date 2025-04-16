<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto\DtoCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Dto\DtoConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\ClassPropertyMapper;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\DateTimePropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\EntityPropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\UuidInterfacePropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\Controller\ControllerClassWriter;
use JtcSolutions\CodeGenerator\Service\Writer\Dto\DtoClassWriter;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\EntityId;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\EntityInterface;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\IRequestDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

abstract class BaseFunctionalTest extends TestCase
{
    // test configuration
    protected const string DEFAULT_CLASS_FQCN = 'JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\TestEntityClass';

    protected const string CONTROLLER_NAMESPACE_TEMPLATE = 'App\{domain}\App\Api\{entity}';

    protected const string DTO_NAMESPACE_TEMPLATE = 'App\{domain}\Domain\Dto\{entity}';

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
                paginationClass: 'App\Pagination',
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
            classPropertyMapper: $this->createClassPropertyMapper(),
            requestDtoInterface: IRequestDto::class,
            ignoredProperties: ['ignoredProperty'],
        );
    }

    protected function createClassPropertyMapper(): ClassPropertyMapper
    {
        $propertyTypeDetectors = [
            new DateTimePropertyTypeDetector(),
            new UuidInterfacePropertyTypeDetector(),
            new EntityPropertyTypeDetector(EntityInterface::class, EntityId::class),
        ];

        return new ClassPropertyMapper(
            propertyTypeDetectors: $propertyTypeDetectors,
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
        );
    }
}
