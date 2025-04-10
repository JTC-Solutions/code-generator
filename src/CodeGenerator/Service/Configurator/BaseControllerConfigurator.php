<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration\ControllerConfigurationBuilder;
use JtcSolutions\Helpers\Helper\FQCNHelper;

abstract class BaseControllerConfigurator
{
    protected const string CONTROLLER_NAME_TEMPLATE = '';

    protected const string NAMESPACE_TEMPLATE = '';

    protected function createBuilder(Context $context): ControllerConfigurationBuilder
    {
        $entityClassName = FQCNHelper::transformFQCNToEntityName($context->entity, false);

        return new ControllerConfigurationBuilder(
            className: sprintf(static::CONTROLLER_NAME_TEMPLATE, $entityClassName),
            namespace: sprintf(static::NAMESPACE_TEMPLATE, $context->domain, $entityClassName),
            method: $this->createMethodConfiguration($context),
        );
    }

    abstract protected function createMethodConfiguration(Context $context): MethodConfiguration|null;
}
