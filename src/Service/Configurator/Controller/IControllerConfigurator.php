<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;

interface IControllerConfigurator
{
    /**
     * @param class-string $classFullyQualifiedClassName
     */
    public function configure(string $classFullyQualifiedClassName): ControllerConfiguration;
}
