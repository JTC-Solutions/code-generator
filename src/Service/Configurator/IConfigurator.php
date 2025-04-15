<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Context;

interface IConfigurator
{
    public function configure(Context $context): IRenderableConfiguration;
}
