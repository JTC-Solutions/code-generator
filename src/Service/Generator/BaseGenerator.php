<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\ICodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\IConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\IClassWriter;

abstract class BaseGenerator
{
    abstract public function generate(Context $context): void;

    abstract public function createCodeRenderer(IRenderableConfiguration $configuration): ICodeRenderer;

    abstract protected function createClassWriter(): IClassWriter;

    abstract protected function createConfigurator(): IConfigurator;
}
