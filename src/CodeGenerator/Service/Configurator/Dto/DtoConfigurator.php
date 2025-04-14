<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Dto;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class DtoConfigurator
{
    public function configure(Context $context, string $prefix = '', string $suffix = ''): DtoConfiguration
    {
        $className = FQCNHelper::transformFQCNToEntityName($context->entityFQCN, false);
        $dtoClassName = $prefix . $className . $suffix;

        $useStatements = [];
        foreach ($context->dtoInterfaces as $dtoInterface) {
            $useStatements[] = new UseStatementConfiguration($dtoInterface);
        }

        return new DtoConfiguration(
            namespace: $context->dtoNamespace,
            className: $dtoClassName,
            useStatements: $useStatements,
            interfaces: $context->dtoInterfaces,
        );
    }
}
