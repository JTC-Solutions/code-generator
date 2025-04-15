<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DetailControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\ControllerClassWriter;

class DetailControllerGenerator extends BaseControllerGenerator
{
    public function __construct(
        DetailControllerConfigurator $configurator,
        ControllerClassWriter $classWriter,
        ControllerCodeRenderer $codeRenderer,
    ) {
        parent::__construct($configurator, $classWriter, $codeRenderer);
    }
}
