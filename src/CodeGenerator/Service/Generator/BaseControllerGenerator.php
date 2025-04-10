<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\IControllerCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\IControllerConfigurator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer\ControllerClassWriter;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer\IControllerClassWriter;

abstract class BaseControllerGenerator
{
    protected const string ENDPOINT_PREFIX = '';

    public function generate(Context $context): void
    {
        $configuration = $this->createConfigurator()->configure($context);
        $code = $this->createCodeRenderer($configuration)->generateCode();

        $this->createClassWriter()->write($context, $configuration->className, static::ENDPOINT_PREFIX, $code);
    }

    public function createCodeRenderer(ControllerConfiguration $configuration): IControllerCodeRenderer
    {
        return new ControllerCodeRenderer($configuration);
    }

    protected function createClassWriter(): IControllerClassWriter
    {
        return new ControllerClassWriter();
    }

    abstract protected function createConfigurator(): IControllerConfigurator;
}
