<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DeleteControllerConfigurator;

class DeleteControllerGenerator extends BaseControllerGenerator
{
    protected function createConfigurator(): DeleteControllerConfigurator
    {
        return new DeleteControllerConfigurator();
    }
}
