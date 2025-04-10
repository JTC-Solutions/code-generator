<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\ListControllerConfigurator;

class ListControllerGenerator extends BaseControllerGenerator
{
    protected const string ENDPOINT_PREFIX = 'List';

    protected function createConfigurator(): ListControllerConfigurator
    {
        return new ListControllerConfigurator();
    }
}
