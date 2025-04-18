<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

interface IMethodAttributeConfiguration extends IConfiguration
{
    public function render(): string;
}
