<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;

interface IControllerConfigurator
{
    public function configure(Context $context): ControllerConfiguration;
}
