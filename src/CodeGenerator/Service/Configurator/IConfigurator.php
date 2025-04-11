<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;

interface IConfigurator
{
    public function configure(Context $context): IRenderableConfiguration;
}
