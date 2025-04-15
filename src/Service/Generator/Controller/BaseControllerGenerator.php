<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\IControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\IControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\ControllerClassWriter;
use JtcSolutions\CodeGenerator\Service\Writer\IControllerClassWriter;

abstract class BaseControllerGenerator
{
    public function generate(
        string $classFullyQualifiedClassName,
    ): void {
        echo 'gg'; die;
        $configuration = $this->createConfigurator()->configure($context);
        $code = $this->createCodeRenderer($configuration)->renderCode();

        $this->createClassWriter()->write($context, $configuration->className, $code);
    }

    protected function createCodeRenderer(ControllerConfiguration $configuration): IControllerCodeRenderer
    {
        return new ControllerCodeRenderer($configuration);
    }

    protected function createClassWriter(): IControllerClassWriter
    {
        return new ControllerClassWriter();
    }

    abstract protected function createConfigurator(): IControllerConfigurator;
}
