<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Dto;

use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class DtoConfigurator
{
    public function __construct(
        private readonly ContextProvider $contextProvider,
    ) {
    }

    public function configure(
        string $classFullyQualifiedClassName,
        string $prefix = '',
        string $suffix = ''
    ): DtoConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $dtoClassName = $prefix . $className . $suffix;

        $useStatements = [];
        foreach ($this->contextProvider->dtoInterfaces as $dtoInterface) {
            $useStatements[] = new UseStatementConfiguration($dtoInterface);
        }

        $interfaces = [];
        foreach ($this->contextProvider->dtoInterfaces as $dtoInterface) {
            $interfaces[] = FQCNHelper::transformFQCNToShortClassName($dtoInterface);
        }

        $dtoNamespace = $this->contextProvider->dtoNamespaceTemplate; // TODO: evaluate ?

        return new DtoConfiguration(
            namespace: $dtoNamespace,
            className: $dtoClassName,
            useStatements: $useStatements,
            interfaces: $interfaces,
        );
    }
}
