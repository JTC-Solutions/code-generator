<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\DetailControllerConfigurator;

class DetailControllerGenerator extends BaseControllerGenerator
{
    protected const string ENDPOINT_PREFIX = 'Detail';

    protected function createConfigurator(): DetailControllerConfigurator
    {
        return new DetailControllerConfigurator();
    }
}
