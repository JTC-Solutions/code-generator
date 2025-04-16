<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DeleteControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\Controller\ControllerClassWriter;

/**
 * Generates a controller responsible for deleting an entity by its ID.
 */
class DeleteControllerGenerator extends BaseControllerGenerator
{
    /**
     * @param DeleteControllerConfigurator $configurator Configurator specific to the 'delete' controller action.
     * @param ControllerClassWriter $classWriter Service to write the generated code to a file.
     * @param ControllerCodeRenderer $codeRenderer Service to render the PHP code from the configuration.
     */
    public function __construct(
        DeleteControllerConfigurator $configurator,
        ControllerClassWriter $classWriter,
        ControllerCodeRenderer $codeRenderer,
    ) {
        parent::__construct($configurator, $classWriter, $codeRenderer);
    }
}
