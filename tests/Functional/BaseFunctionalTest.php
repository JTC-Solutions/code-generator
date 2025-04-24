<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto\DtoCodeRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Service\ServiceCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\CreateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DeleteControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DetailControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\ListControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\UpdateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Configurator\Dto\DtoConfigurator;
use JtcSolutions\CodeGenerator\Service\Configurator\Service\ServiceConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\CreateControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\DeleteControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\DetailControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\ListControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\UpdateControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Service\ServiceGenerator;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\ClassPropertyMapper;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\DateTimePropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\EntityPropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\PropertyTypeDetector\UuidInterfacePropertyTypeDetector;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\Controller\ControllerClassWriter;
use JtcSolutions\CodeGenerator\Service\Writer\Dto\DtoClassWriter;
use JtcSolutions\CodeGenerator\Service\Writer\Service\ServiceClassWriter;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\EntityId;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\EntityInterface;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\IRequestDto;
use JtcSolutions\Core\Controller\BaseController;
use JtcSolutions\Core\Controller\BaseEntityCRUDController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

abstract class BaseFunctionalTest extends TestCase
{
    // test configuration
    protected const string DEFAULT_CLASS_FQCN = 'JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\TestEntityClass';

    protected const string CONTROLLER_NAMESPACE_TEMPLATE = 'App\{domain}\App\Api\{entity}';

    protected const string DTO_NAMESPACE_TEMPLATE = 'App\{domain}\Domain\Dto\{entity}';

    protected const string SERVICE_NAMESPACE_TEMPLATE = 'App\{domain}\Domain\Service';

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
            suffix: 'RequestBody'
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
                serviceNamespaceTemplate: static::SERVICE_NAMESPACE_TEMPLATE,
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

    protected function createServiceGenerator(): ServiceGenerator
    {
        return new ServiceGenerator(
            classWriter: $this->createServiceClassWriter(),
            configurator: $this->createServiceConfigurator(),
            codeRenderer: $this->createServiceCodeRenderer(),
        );
    }

    protected function createServiceConfigurator(): ServiceConfigurator
    {
        return new ServiceConfigurator(
            contextProvider: $this->createContextProvider(),
            classPropertyMapper: $this->createClassPropertyMapper(),
            ignoredProperties: ['id', 'ignoredProperty'],
        );
    }

    protected function createServiceClassWriter(): ServiceClassWriter
    {
        return new ServiceClassWriter($this->createContextProvider());
    }

    protected function createServiceCodeRenderer(): ServiceCodeRenderer
    {
        return new ServiceCodeRenderer();
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

    protected function createCreateControllerGenerator(): CreateControllerGenerator
    {
        return new CreateControllerGenerator(
            contextProvider: $this->createContextProvider(),
            configurator: $this->createCreateControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createCreateControllerConfigurator(): CreateControllerConfigurator
    {
        return new CreateControllerConfigurator(
            contextProvider: $this->createContextProvider(),
            defaultParent: BaseEntityCRUDController::class,
        );
    }

    protected function createDeleteControllerGenerator(): DeleteControllerGenerator
    {
        return new DeleteControllerGenerator(
            configurator: $this->createDeleteControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createDeleteControllerConfigurator(): DeleteControllerConfigurator
    {
        return new DeleteControllerConfigurator(
            contextProvider: $this->createContextProvider(),
            defaultParent: BaseEntityCRUDController::class,
        );
    }

    protected function createDetailControllerGenerator(): DetailControllerGenerator
    {
        return new DetailControllerGenerator(
            configurator: $this->createDetailControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createDetailControllerConfigurator(): DetailControllerConfigurator
    {
        return new DetailControllerConfigurator(
            contextProvider: $this->createContextProvider(),
            defaultParent: BaseController::class,
        );
    }

    protected function createListControllerGenerator(): ListControllerGenerator
    {
        return new ListControllerGenerator(
            configurator: $this->createListControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createListControllerConfigurator(): ListControllerConfigurator
    {
        return new ListControllerConfigurator(
            contextProvider: $this->createContextProvider(),
            defaultParent: BaseController::class,
        );
    }

    protected function createUpdateControllerGenerator(): UpdateControllerGenerator
    {
        return new UpdateControllerGenerator(
            contextProvider: $this->createContextProvider(),
            configurator: $this->createUpdateControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createUpdateControllerConfigurator(): UpdateControllerConfigurator
    {
        return new UpdateControllerConfigurator(
            contextProvider: $this->createContextProvider(),
            defaultParent: BaseEntityCRUDController::class,
        );
    }
}
