<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\IConfiguration;

interface IMethodAttributeConfiguration extends IConfiguration
{
    public function render(): string;
}
