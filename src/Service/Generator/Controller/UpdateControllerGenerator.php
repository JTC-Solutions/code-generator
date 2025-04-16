<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\UpdateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\Controller\ControllerClassWriter;

/**
 * Generates a controller responsible for updating an existing entity.
 * This generator first generates the required Request DTO using DtoGenerator.
 */
class UpdateControllerGenerator extends BaseControllerGenerator
{
    /**
     * @param DtoGenerator $dtoGenerator Generator for the request DTO.
     * @param ContextProvider $contextProvider Provides context like namespaces and paths, and holds the generated DTO FQCN.
     * @param UpdateControllerConfigurator $configurator Configurator specific to the 'update' controller action.
     * @param ControllerClassWriter $classWriter Service to write the generated code to a file.
     * @param ControllerCodeRenderer $codeRenderer Service to render the PHP code from the configuration.
     * @param string $dtoNamePrefix Prefix to add to the generated DTO class name.
     * @param string $dtoNameSuffix Suffix to add to the generated DTO class name.
     */
    public function __construct(
        protected readonly DtoGenerator $dtoGenerator,
        protected readonly ContextProvider $contextProvider,
        UpdateControllerConfigurator $configurator,
        ControllerClassWriter $classWriter,
        ControllerCodeRenderer $codeRenderer,
        protected readonly string $dtoNamePrefix = '',
        protected readonly string $dtoNameSuffix = '',
    ) {
        parent::__construct($configurator, $classWriter, $codeRenderer);
    }

    public function generate(string $classFullyQualifiedClassName): void
    {
        $dtoFullyQualifiedClassName = $this->dtoGenerator->generate($classFullyQualifiedClassName, $this->dtoNamePrefix, $this->dtoNameSuffix);

        $this->contextProvider->dtoFullyQualifiedClassName = $dtoFullyQualifiedClassName;

        parent::generate($classFullyQualifiedClassName);
    }
}
