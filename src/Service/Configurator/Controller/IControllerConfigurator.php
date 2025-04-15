<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;

interface IControllerConfigurator
{
    public function configure(string $classFullyQualifiedClassName): ControllerConfiguration;
}
