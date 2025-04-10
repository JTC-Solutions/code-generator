<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\CreateControllerConfigurator;

class CreateControllerGenerator extends BaseControllerGenerator
{
    protected const string ENDPOINT_PREFIX = 'Create';

    protected function createConfigurator(): CreateControllerConfigurator
    {
        return new CreateControllerConfigurator();
    }
}
