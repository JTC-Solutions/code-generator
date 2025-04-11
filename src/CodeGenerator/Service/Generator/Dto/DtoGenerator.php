<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Dto;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Dto\DtoCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Dto\DtoConfigurator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer\DtoClassWriter;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class DtoGenerator
{
    public function generate(Context $context, string $prefix = '', string $suffix = ''): string
    {
        $configuration = $this->createConfigurator()->configure($context, $prefix, $suffix);
        $code = $this->createCodeRenderer($configuration)->renderCode();

        $className = FQCNHelper::transformFQCNToEntityName($context->entityFQCN, false);
        $dtoClassName = $prefix . $className . $suffix;

        return $this->createClassWriter()->write($context, $dtoClassName, $code);
    }

    public function createCodeRenderer(DtoConfiguration $configuration): DtoCodeRenderer
    {
        return new DtoCodeRenderer($configuration);
    }

    protected function createClassWriter(): DtoClassWriter
    {
        return new DtoClassWriter();
    }

    protected function createConfigurator(): DtoConfigurator
    {
        return new DtoConfigurator();
    }
}
