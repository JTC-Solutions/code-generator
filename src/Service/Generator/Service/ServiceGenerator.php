<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Service;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Service\ServiceCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Service\ServiceConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\Service\ServiceClassWriter;
use JtcSolutions\Core\Entity\IEntity;

class ServiceGenerator
{
    public function __construct(
        protected readonly ServiceClassWriter $classWriter,
        protected readonly ServiceConfigurator $configurator,
        protected readonly ServiceCodeRenderer $codeRenderer,
    ) {
    }

    /**
     * @param class-string<IEntity> $classFullyQualifiedClassName
     * @return class-string
     */
    public function generate(string $classFullyQualifiedClassName): string
    {
        $configuration = $this->configurator->configure($classFullyQualifiedClassName);
        $code = $this->codeRenderer->renderCode($configuration);

        $serviceFullyQualifiedClassName = $configuration->getFullyQualifiedClassName();

        $this->classWriter->write($classFullyQualifiedClassName, $serviceFullyQualifiedClassName, $code);

        return $serviceFullyQualifiedClassName;
    }
}
