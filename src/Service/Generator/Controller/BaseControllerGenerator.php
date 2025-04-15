<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\IControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\IControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\IControllerClassWriter;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

abstract class BaseControllerGenerator
{
    public function __construct(
        protected readonly IControllerConfigurator $configurator,
        protected readonly IControllerClassWriter $classWriter,
        protected readonly IControllerCodeRenderer $codeRenderer,
    ) {
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     */
    public function generate(string $classFullyQualifiedClassName): void
    {
        $configuration = $this->configurator->configure($classFullyQualifiedClassName);
        $code = $this->codeRenderer->renderCode($configuration);

        $this->classWriter->write($classFullyQualifiedClassName, $configuration->className, $code);
    }
}
