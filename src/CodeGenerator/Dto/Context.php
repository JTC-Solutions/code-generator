<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto;

class Context
{
    /**
     * @param class-string $entityFQCN
     * @param string[] $defaultUseStatements
     */
    public function __construct(
        public readonly string $entityFQCN,
        public readonly string $entityNamespace,
        public readonly string $entityPath,
        public readonly string $controllerPath,
        public readonly string $controllerNamespace,
        public readonly string $dtoPath,
        public readonly string $dtoNamespace,
        public array $defaultUseStatements = [],
    ) {
    }
}
