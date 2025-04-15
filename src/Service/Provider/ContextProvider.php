<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Provider;

use JtcSolutions\Helpers\Helper\FQCNHelper;

class ContextProvider
{
    public function __construct(
        public readonly string $controllerNamespaceTemplate,
        public readonly string $dtoNamespaceTemplate,
        public readonly string $projectDir,
        public readonly string $projectBaseNamespace,
        public readonly string $errorResponseClass,
        public array $extendedClasses = [],
        public array $dtoInterfaces = [],
    ) {
    }

    public function getDtoPath(): string
    {
        return FQCNHelper::convertNamespaceToFilepath($this->dtoNamespaceTemplate, $this->projectBaseNamespace, $this->projectDir);
    }

    public function getControllerPath(): string
    {
        return FQCNHelper::convertNamespaceToFilepath($this->controllerNamespaceTemplate, $this->projectBaseNamespace, $this->projectDir);
    }

    public function getDtoNamespace(): string
    {
        return $this->dtoNamespaceTemplate; // TODO: Fix, add evaluate
    }

    public function getControllerNamespace(): string
    {
        return $this->controllerNamespaceTemplate; // TODO: Fix, add evaluate
    }

    public function getExtendedClasses(): array
    {
        return $this->extendedClasses;
    }

    /**
     * @return class-string
     */
    public function getErrorResponseClass(): string
    {
        return $this->errorResponseClass;
    }
}
