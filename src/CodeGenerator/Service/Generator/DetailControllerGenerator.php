<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\DomainContext;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\ClassWriter;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\DetailControllerConfigurator;

class DetailControllerGenerator
{
    /**
     * @throws ConfigurationException
     */
    public function generate(DomainContext $context): void
    {
        $configuration = $this->createConfigurator()->configure($context->domain, $context->entity);
        $code = $this->createCodeRenderer($configuration)->generateCode();
        $this->createClassWriter()->write($code);
    }

    protected function createClassWriter(): ClassWriter
    {
        return new ClassWriter();
    }

    protected function createConfigurator(): DetailControllerConfigurator
    {
        return new DetailControllerConfigurator();
    }

    protected function createCodeRenderer(ControllerConfiguration $configuration): ControllerCodeRenderer
    {
        return new ControllerCodeRenderer($configuration);
    }
}
