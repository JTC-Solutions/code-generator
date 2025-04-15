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
    protected const string CONTROLLER_NAME_TEMPLATE = '';

    protected const bool CALL_PARENT_CONSTRUCTOR = false;

    public function __construct(
        protected readonly ContextProvider $contextProvider,
    ) {
    }

    /**
     * @throws ConfigurationException
     * @throws Exception
     */
    protected function createBuilder(string $classFullyQualifiedClassName): ControllerConfigurationBuilder
    {
        if (static::CONTROLLER_NAME_TEMPLATE === '') {
            throw new Exception(
                sprintf(
                    'Class %s is extending %s but it does not have defined const CONTROLLER_NAME_TEMPLATE',
                    static::class,
                    self::class,
                ),
            );
        }

        $entityClassName = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);

        $builder = new ControllerConfigurationBuilder(
            className: sprintf(static::CONTROLLER_NAME_TEMPLATE, $entityClassName),
            namespace: $this->contextProvider->getControllerNamespace($classFullyQualifiedClassName),
            method: $this->createMethodConfiguration($classFullyQualifiedClassName),
            callParent: static::CALL_PARENT_CONSTRUCTOR,
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
