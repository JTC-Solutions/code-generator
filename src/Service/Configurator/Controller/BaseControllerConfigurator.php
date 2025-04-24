<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstract base class for controller configurators.
 * Provides common functionality for building controller configurations.
 */
abstract class BaseControllerConfigurator
{
    /**
     * @param ContextProvider $contextProvider Provides context like namespaces, paths, and shared configuration (e.g., DTO FQCN).
     * @param string $methodName The name of the primary method in the generated controller (e.g., 'create', 'list').
     * @param string $controllerNameTemplate A template string (using sprintf) for the controller class name (e.g., 'Create%sController').
     * @param bool $callParentConstructor Whether the generated controller's constructor should call parent::__construct().
     * @param class-string $defaultExtendedClass The default class to extend
     */
    public function __construct(
        protected readonly ContextProvider $contextProvider,
        protected readonly string $methodName,
        protected readonly string $controllerNameTemplate,
        protected readonly bool $callParentConstructor,
        protected readonly string $defaultExtendedClass,
    ) {
    }

    /**
     * Creates and initializes the ControllerConfigurationBuilder.
     * Sets up namespace, class name, extends, and initial use statements.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return ControllerConfigurationBuilder An initialized builder instance.
     * @throws ConfigurationException If adding initial configuration (like extends or use statements) fails.
     * @throws Exception If FQCN parsing fails.
     */
    protected function createBuilder(string $classFullyQualifiedClassName): ControllerConfigurationBuilder
    {
        $entityClassName = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);

        $builder = new ControllerConfigurationBuilder(
            className: sprintf($this->controllerNameTemplate, $entityClassName),
            namespace: $this->contextProvider->getControllerNamespace($classFullyQualifiedClassName),
            method: $this->createMethodConfiguration($classFullyQualifiedClassName),
            callParent: $this->callParentConstructor,
            constructorBody: $this->configureConstructorBody($classFullyQualifiedClassName),
        );

        if ($this->contextProvider->getExtendedClasses() === []) {
            $builder->addExtendedClass($this->defaultExtendedClass);
        } else {
            foreach ($this->contextProvider->getExtendedClasses() as $extendedClass) {
                $builder->addExtendedClass($extendedClass);
            }
        }

        $this->configureUseStatements($builder, $classFullyQualifiedClassName);

        return $builder;
    }

    /**
     * Configures common use statements required by most generated controllers.
     *
     * @param ControllerConfigurationBuilder $builder The builder instance to add use statements to.
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity (unused in base, but available for overrides).
     * @throws ConfigurationException If adding use statements fails.
     */
    protected function configureUseStatements(
        ControllerConfigurationBuilder $builder,
        string $classFullyQualifiedClassName,
    ): void {
        /** @phpstan-ignore-next-line */
        $builder->addUseStatement("OpenApi\Attributes", 'OA');
        $builder->addUseStatement(Response::class);
        $builder->addUseStatement($this->contextProvider->getErrorResponseClass());

        if ($this->contextProvider->dtoFullyQualifiedClassName !== null) {
            $builder->addUseStatement($this->contextProvider->dtoFullyQualifiedClassName);
        }

        if ($this->contextProvider->serviceFullyQualifiedClassName !== null) {
            $builder->addUseStatement($this->contextProvider->serviceFullyQualifiedClassName);
        }
    }

    /**
     * Configures the body content of the constructor, if any.
     * Base implementation returns null (no body). Can be overridden by subclasses.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return string|null The code snippet for the constructor body, or null if none.
     */
    protected function configureConstructorBody(string $classFullyQualifiedClassName): ?string
    {
        return null;
    }

    /**
     * Abstract method to create the specific method configuration for the controller action.
     * Subclasses must implement this to define the controller's main method (e.g., list(), create(), detail()).
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the target entity.
     * @return MethodConfiguration|null The configuration for the controller's primary method, or null if no method should be generated.
     * @throws ConfigurationException If building the method configuration fails.
     */
    abstract protected function createMethodConfiguration(string $classFullyQualifiedClassName): MethodConfiguration|null;
}
