<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\ListControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\Controller\ControllerClassWriter;

/**
 * Generates a controller responsible for fetching and displaying a list of entities,
 * potentially with pagination and filtering.
 */
class ListControllerGenerator extends BaseControllerGenerator
{
    /**
     * @param ListControllerConfigurator $configurator Configurator specific to the 'list' controller action.
     * @param ControllerClassWriter $classWriter Service to write the generated code to a file.
     * @param ControllerCodeRenderer $codeRenderer Service to render the PHP code from the configuration.
     */
    public function __construct(
        ListControllerConfigurator $configurator,
        ControllerClassWriter $classWriter,
        ControllerCodeRenderer $codeRenderer,
    ) {
        parent::__construct($configurator, $classWriter, $codeRenderer);
    }
}
