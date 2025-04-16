<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Dto;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto\DtoCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Dto\DtoConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\DtoClassWriter;

class DtoGenerator
{
    public function __construct(
        protected readonly DtoClassWriter $classWriter,
        protected readonly DtoConfigurator $configurator,
        protected readonly DtoCodeRenderer $codeRenderer,
    ) {
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     * @return class-string DTO's FQCN
     */
    public function generate(
        string $classFullyQualifiedClassName,
        string $prefix = '',
        string $suffix = '',
    ): string {
        $configuration = $this->configurator->configure($classFullyQualifiedClassName, $prefix, $suffix);
        $code = $this->codeRenderer->renderCode($configuration);

        $dtoFullyQualifiedClassName = $configuration->getFullyQualifiedClassName();

        $this->classWriter->write($classFullyQualifiedClassName, $dtoFullyQualifiedClassName, $code);

        return $dtoFullyQualifiedClassName;
    }
}
