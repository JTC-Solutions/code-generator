<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Controller\ListControllerConfigurator;

class ListControllerGenerator extends BaseControllerGenerator
{
    protected function createConfigurator(): ListControllerConfigurator
    {
        return new ListControllerConfigurator();
    }
}
