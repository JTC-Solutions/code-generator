<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JtcSolutions\CodeGenerator\DependencyInjection\Configuration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Service\ServiceConfiguration;
use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\MethodConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\ServiceConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\ClassPropertyMapper;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Provider\DefaultServiceMethodTemplateProvider;
use JtcSolutions\Core\Dto\IEntityRequestBody;
use JtcSolutions\Core\Entity\IEntity;
use JtcSolutions\Core\Service\BaseEntityService;
use JtcSolutions\Core\Service\IEntityService;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use JtcSolutions\Helpers\Helper\StringUtils;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * Configures the structure and content of a new Entity Service class based on an existing Entity class.
 * It determines the namespace, class name, extended classes, implemented interfaces,
 * constructor parameters, and methods (like create, update, delete) for the service.
 */
class ServiceConfigurator
{
    /**
     * @param ContextProvider $contextProvider Provides context like target namespaces.
     * @param ClassPropertyMapper $classPropertyMapper Maps properties from the entity class.
     * @param string[] $ignoredProperties Properties that will be skipped during service generation.
     */
    public function __construct(
        private readonly ContextProvider $contextProvider,
        private readonly ClassPropertyMapper $classPropertyMapper,
        private readonly array $ignoredProperties,
    ) {
    }

    /**
     * Generates the configuration DTO for a new service based on the provided entity class.
     *
     * @param class-string<IEntity> $classFullyQualifiedClassName The FQCN of the class to base the Service on.
     * @return ServiceConfiguration DTO containing all configuration details for the service class.
     * @throws ConfigurationException
     * @throws Exception
     * @throws ReflectionException
     */
    public function configure(string $classFullyQualifiedClassName): ServiceConfiguration
    {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $serviceClassName = $className . 'Service';

        $builder = new ServiceConfigurationBuilder(
            className: $serviceClassName,
            namespace: $this->contextProvider->getServiceNamespace($classFullyQualifiedClassName),
        );

        $builder
            ->addInterface(IEntityService::class)
            ->addExtendedClass(BaseEntityService::class)
            ->addConstructorParam(new MethodArgumentConfiguration('repository', Configuration::class)) // TODO: Replace with generated repo
            ->addConstructorParam(new MethodArgumentConfiguration('entityManager', EntityManagerInterface::class, false, 'private', true));

        $builder->addUseStatement($classFullyQualifiedClassName);
        $builder->addUseStatement(IEntity::class);
        $builder->addUseStatement(UuidInterface::class);
        $builder->addUseStatement(Uuid::class);
        $builder->addUseStatement(IEntityRequestBody::class);
        $builder->addUseStatement(EntityManagerInterface::class);

        $arguments = $this->getMethodArguments($classFullyQualifiedClassName);

        $properties = $this->classPropertyMapper->getPropertyMap($classFullyQualifiedClassName);
        $filteredProperties = [];
        foreach ($properties as $property) {
            if (! in_array($property->name, $this->ignoredProperties, true)) {
                $filteredProperties[] = $property;
                if ($property->useStatement !== null) {
                    $builder->addUseStatement($property->useStatement);
                }
            }
        }

        $this->configureCreateMethod($builder, $arguments, $classFullyQualifiedClassName, $filteredProperties);
        $this->configureUpdateMethod($builder, $arguments, $classFullyQualifiedClassName, $filteredProperties);
        $this->configureDeleteMethod($builder, $classFullyQualifiedClassName, $filteredProperties);

        $this->configureCreateMapMethod($builder, $classFullyQualifiedClassName, $filteredProperties);
        $this->configureUpdateMapMethod($builder, $classFullyQualifiedClassName, $filteredProperties);

        return $builder->build();
    }

    /**
     * Configures the 'create' method for the service.
     *
     * @param ServiceConfigurationBuilder $builder The builder instance for the service configuration.
     * @param MethodArgumentConfiguration[] $arguments Arguments derived from the entity properties (excluding ignored ones).
     * @param class-string<IEntity> $classFullyQualifiedClassName The FQCN of the source entity.
     * @param MappedProperty[] $properties Mapped properties of the entity (excluding ignored ones).
     * @throws ConfigurationException
     */
    protected function configureCreateMethod(
        ServiceConfigurationBuilder $builder,
        array $arguments,
        string $classFullyQualifiedClassName,
        array $properties,
    ): void {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $createMethod = new MethodConfigurationBuilder(
            name: 'create',
            returnType: $className,
            body: DefaultServiceMethodTemplateProvider::provideCreateMethodTemplate($classFullyQualifiedClassName, $properties),
        );

        foreach ($arguments as $argument) {
            $createMethod->addArgument($argument);
        }

        $builder->addMethodConfiguration($createMethod->build());
    }

    /**
     * Configures the 'update' method for the service.
     *
     * @param ServiceConfigurationBuilder $builder The builder instance for the service configuration.
     * @param MethodArgumentConfiguration[] $arguments Arguments derived from the entity properties (excluding ignored ones).
     * @param class-string<IEntity> $classFullyQualifiedClassName The FQCN of the source entity.
     * @param MappedProperty[] $properties Mapped properties of the entity (excluding ignored ones).
     * @throws ConfigurationException
     */
    protected function configureUpdateMethod(
        ServiceConfigurationBuilder $builder,
        array $arguments,
        string $classFullyQualifiedClassName,
        array $properties,
    ): void {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $updateMethod = new MethodConfigurationBuilder(
            name: 'update',
            returnType: $className,
            body: DefaultServiceMethodTemplateProvider::provideUpdateMethodTemplate($classFullyQualifiedClassName, $properties),
        );

        // add entity to update as a first argument
        $updateMethod->addArgument(new MethodArgumentConfiguration(StringUtils::firstToLowercase($className), $className));

        foreach ($arguments as $argument) {
            $updateMethod->addArgument($argument);
        }

        $builder->addMethodConfiguration($updateMethod->build());
    }

    /**
     * Configures the 'delete' method for the service.
     *
     * @param ServiceConfigurationBuilder $builder The builder instance for the service configuration.
     * @param class-string<IEntity> $classFullyQualifiedClassName The FQCN of the source entity.
     * @param MappedProperty[] $properties Mapped properties of the entity (excluding ignored ones). Used to generate method body template.
     * @throws ConfigurationException
     */
    protected function configureDeleteMethod(
        ServiceConfigurationBuilder $builder,
        string $classFullyQualifiedClassName,
        array $properties,
    ): void {
        $deleteMethod = new MethodConfigurationBuilder(
            name: 'delete',
            returnType: 'void',
            body: DefaultServiceMethodTemplateProvider::provideDeleteMethodTemplate($classFullyQualifiedClassName, $properties),
        );

        $uuidClassName = FQCNHelper::transformFQCNToShortClassName(UuidInterface::class);
        $entityInterface = FQCNHelper::transformFQCNToShortClassName(IEntity::class);

        $deleteMethod->addArgument(
            new MethodArgumentConfiguration(
                argumentName: 'id',
                argumentType: sprintf('%s|%s', $uuidClassName, $entityInterface),
            ),
        );

        $builder->addMethodConfiguration($deleteMethod->build());
    }

    /**
     * Configures the 'mapDataAndCallCreate' method, which likely handles mapping from a request DTO before creating an entity.
     *
     * @param ServiceConfigurationBuilder $builder The builder instance for the service configuration.
     * @param class-string<IEntity> $classFullyQualifiedClassName The FQCN of the source entity.
     * @param MappedProperty[] $properties Mapped properties of the entity (excluding ignored ones).
     * @throws ConfigurationException
     */
    protected function configureCreateMapMethod(
        ServiceConfigurationBuilder $builder,
        string $classFullyQualifiedClassName,
        array $properties,
    ): void {
        $requestDtoInterface = FQCNHelper::transformFQCNToShortClassName(IEntityRequestBody::class);
        $entityInterface = FQCNHelper::transformFQCNToShortClassName(IEntity::class);

        $mapDataAndCallCreateMethod = new MethodConfigurationBuilder(
            name: 'mapDataAndCallCreate',
            returnType: $entityInterface,
            body: DefaultServiceMethodTemplateProvider::provideMapDataAndCallCreateMethodTemplate($classFullyQualifiedClassName, $properties),
        );

        $mapDataAndCallCreateMethod->addArgument(
            new MethodArgumentConfiguration(
                argumentName: 'requestBody',
                argumentType: $requestDtoInterface,
            ),
        );

        $builder->addMethodConfiguration($mapDataAndCallCreateMethod->build());
    }

    /**
     * Configures the 'mapDataAndCallUpdate' method, which likely handles mapping from a request DTO before updating an entity.
     * Note: The method name might have a typo and should perhaps be 'mapDataAndCallUpdate'.
     *
     * @param ServiceConfigurationBuilder $builder The builder instance for the service configuration.
     * @param class-string<IEntity> $classFullyQualifiedClassName The FQCN of the source entity.
     * @param MappedProperty[] $properties Mapped properties of the entity (excluding ignored ones).
     * @throws ConfigurationException
     */
    protected function configureUpdateMapMethod(
        ServiceConfigurationBuilder $builder,
        string $classFullyQualifiedClassName,
        array $properties,
    ): void {
        $requestDtoInterface = FQCNHelper::transformFQCNToShortClassName(IEntityRequestBody::class);
        $entityInterface = FQCNHelper::transformFQCNToShortClassName(IEntity::class);
        $uuidInterface = FQCNHelper::transformFQCNToShortClassName(UuidInterface::class);
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $classVariableName = StringUtils::firstToLowercase($className);
        $classIdVariableName = $classVariableName . 'Id';

        $mapDataAndCallUpdateMethod = new MethodConfigurationBuilder(
            name: 'mapDataAndCallUpdate',
            returnType: $entityInterface,
            body: DefaultServiceMethodTemplateProvider::provideMapDataAndCallUpdateMethodTemplate($classFullyQualifiedClassName, $properties),
        );

        $mapDataAndCallUpdateMethod->addArgument(
            new MethodArgumentConfiguration(
                argumentName: $classIdVariableName,
                argumentType: sprintf('%s|%s', $uuidInterface, $entityInterface),
            ),
        );

        $mapDataAndCallUpdateMethod->addArgument(
            new MethodArgumentConfiguration(
                argumentName: 'requestBody',
                argumentType: $requestDtoInterface,
            ),
        );

        $builder->addMethodConfiguration($mapDataAndCallUpdateMethod->build());
    }

    /**
     * Derives method arguments based on the properties of the entity class, excluding ignored properties.
     * Used primarily for the 'create' and 'update' methods.
     *
     * @param class-string<IEntity> $classFullyQualifiedClassName The FQCN of the source entity.
     * @return MethodArgumentConfiguration[] An array of argument configurations.
     * @throws ReflectionException
     */
    protected function getMethodArguments(string $classFullyQualifiedClassName): array
    {
        $arguments = [];

        $propertyMap = $this->classPropertyMapper->getPropertyMap($classFullyQualifiedClassName);
        foreach ($propertyMap as $property) {
            if (! in_array($property->name, $this->ignoredProperties, true)) {
                $arguments[] = new MethodArgumentConfiguration(
                    argumentName: $property->name,
                    argumentType: $property->type,
                );
            }
        }

        return $arguments;
    }
}
