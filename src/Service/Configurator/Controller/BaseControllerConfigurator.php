<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseControllerConfigurator
{
    public function __construct(
        protected readonly ContextProvider $contextProvider,
        protected readonly string $methodName,
        protected readonly string $controllerNameTemplate,
        protected readonly bool $callParentConstructor,
    ) {
    }

    /**
     * @throws ConfigurationException
     * @throws Exception
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
            $builder->addExtendedClass(AbstractController::class);
        } else {
            foreach ($this->contextProvider->getExtendedClasses() as $extendedClass) {
                $builder->addExtendedClass($extendedClass);
            }
        }

        $this->configureUseStatements($builder, $classFullyQualifiedClassName);

        return $builder;
    }

    /**
     * @throws ConfigurationException
     */
    protected function configureUseStatements(
        ControllerConfigurationBuilder $builder,
        string $classFullyQualifiedClassName,
    ): void {
        $builder->addUseStatement("OpenApi\Attributes", 'OA');
        $builder->addUseStatement(Response::class);
        $builder->addUseStatement($this->contextProvider->getErrorResponseClass());

        if ($this->contextProvider->dtoFullyQualifiedClassName !== null) {
            $builder->addUseStatement($this->contextProvider->dtoFullyQualifiedClassName);
        }
    }

    protected function configureConstructorBody(string $classFullyQualifiedClassName): ?string
    {
        return null;
    }

    abstract protected function createMethodConfiguration(string $classFullyQualifiedClassName): MethodConfiguration|null;
}
