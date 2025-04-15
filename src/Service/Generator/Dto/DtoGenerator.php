<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Dto;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Dto\DtoCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Dto\DtoConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\DtoClassWriter;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class DtoGenerator
{
    public function __construct(
        protected readonly DtoClassWriter $classWriter,
        protected readonly DtoConfigurator $configurator,
        protected readonly DtoCodeRenderer $codeRenderer,
    ) {
    }

    public function generate(
        string $classFullyQualifiedClassName,
        string $prefix = '',
        string $suffix = '',
    ): void {
        $configuration = $this->configurator->configure($classFullyQualifiedClassName, $prefix, $suffix);
        $code = $this->codeRenderer->renderCode($configuration);

        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $dtoClassName = $prefix . $className . $suffix;

        $this->classWriter->write($classFullyQualifiedClassName, $dtoClassName, $code);
    }
}
