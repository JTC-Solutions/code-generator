<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\UpdateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\ControllerClassWriter;

class UpdateControllerGenerator extends BaseControllerGenerator
{
    protected const string DTO_SUFFIX = 'UpdateRequest';

    public function __construct(
        protected readonly DtoGenerator $dtoGenerator,
        protected readonly ContextProvider $contextProvider,
        UpdateControllerConfigurator $configurator,
        ControllerClassWriter $classWriter,
        ControllerCodeRenderer $codeRenderer,
    ) {
        parent::__construct($configurator, $classWriter, $codeRenderer);
    }

    public function generate(string $classFullyQualifiedClassName): void
    {
        $this->dtoGenerator->generate($classFullyQualifiedClassName, '', static::DTO_SUFFIX);
        $dtoNamespace = $this->contextProvider->getDtoNamespace();
        $dtoNamespace = str_replace('.php', '', $dtoNamespace);

        // TODO: add dto namespace use statements

        parent::generate($classFullyQualifiedClassName);
    }
}
