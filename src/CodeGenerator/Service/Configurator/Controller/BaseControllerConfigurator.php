<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Controller;

use Exception;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\Helpers\Helper\FQCNHelper;

abstract class BaseControllerConfigurator
{
    protected const string CONTROLLER_NAME_TEMPLATE = '';

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
        );

        $this->configureUseStatements($builder, $context);

        return $builder;
    }

    abstract protected function createMethodConfiguration(Context $context): MethodConfiguration|null;

    /**
     * @throws ConfigurationException
     */
    protected function configureUseStatements(ControllerConfigurationBuilder $builder, Context $context): void
    {
        foreach ($context->defaultUseStatements as $defaultUseStatement) {
            $builder->addUseStatement($defaultUseStatement);
        }
    }
}
