<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\CreateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\ControllerClassWriter;

class CreateControllerGenerator extends BaseControllerGenerator
{
    public function __construct(
        protected readonly DtoGenerator $dtoGenerator,
        protected readonly ContextProvider $contextProvider,
        CreateControllerConfigurator $configurator,
        ControllerClassWriter $classWriter,
        ControllerCodeRenderer $codeRenderer,
        protected readonly string $dtoNamePrefix = '',
        protected readonly string $dtoNameSuffix = '',
    ) {
        parent::__construct($configurator, $classWriter, $codeRenderer);
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     */
    public function generate(string $classFullyQualifiedClassName): void
    {
        $dtoFullyQualifiedClassName = $this->dtoGenerator->generate($classFullyQualifiedClassName, $this->dtoNamePrefix, $this->dtoNameSuffix);

        $this->contextProvider->dtoFullyQualifiedClassName = $dtoFullyQualifiedClassName;

        parent::generate($classFullyQualifiedClassName);
    }
}
