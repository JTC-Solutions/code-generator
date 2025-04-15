<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseControllerConfigurator
{
    protected const string CONTROLLER_NAME_TEMPLATE = '';

    protected const bool CALL_PARENT_CONSTRUCTOR = false;

    /**
     * @throws ConfigurationException
     * @throws Exception
     */
    protected function createBuilder(Context $context): ControllerConfigurationBuilder
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

        $entityClassName = FQCNHelper::transformFQCNToEntityName($context->entityFQCN, false);

        $builder = new ControllerConfigurationBuilder(
            className: sprintf(static::CONTROLLER_NAME_TEMPLATE, $entityClassName),
            namespace: $context->controllerNamespace,
            method: $this->createMethodConfiguration($context),
            callParent: static::CALL_PARENT_CONSTRUCTOR,
            constructorBody: $this->configureConstructorBody($context)
        );

        if ($context->extendedClasses === []) {
            $builder->addExtendedClass(AbstractController::class);
        } else {
            foreach ($context->extendedClasses as $extendedClass) {
                $builder->addExtendedClass($extendedClass);
            }
        }

        $this->configureUseStatements($builder, $context);

        return $builder;
    }

    protected function configureConstructorBody(Context $context): ?string
    {
        return null;
    }

    abstract protected function createMethodConfiguration(Context $context): MethodConfiguration|null;

    /**
     * @throws ConfigurationException
     */
    protected function configureUseStatements(ControllerConfigurationBuilder $builder, Context $context): void
    {
        $builder->addUseStatement("OpenApi\Attributes", 'OA');
        $builder->addUseStatement(Response::class);
        $builder->addUseStatement($context->errorResponseClass);

        foreach ($context->defaultUseStatements as $defaultUseStatement) {
            $builder->addUseStatement($defaultUseStatement);
        }
    }
}
