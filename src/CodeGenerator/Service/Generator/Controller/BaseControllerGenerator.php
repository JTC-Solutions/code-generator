<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Controller\IControllerCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Controller\IControllerConfigurator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer\ControllerClassWriter;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer\IControllerClassWriter;

abstract class BaseControllerGenerator
{
    public function generate(Context $context): void
    {
        $configuration = $this->createConfigurator()->configure($context);
        $code = $this->createCodeRenderer($configuration)->renderCode();

        $this->createClassWriter()->write($context, $configuration->className, $code);
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
