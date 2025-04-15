<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DetailControllerConfigurator;

class DetailControllerGenerator extends BaseControllerGenerator
{
    protected function createConfigurator(): DetailControllerConfigurator
    {
        return new DetailControllerConfigurator();
    }
}
