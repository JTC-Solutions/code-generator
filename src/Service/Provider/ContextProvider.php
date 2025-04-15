<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Provider;

use JtcSolutions\Helpers\Helper\FQCNHelper;

class ContextProvider
{
    /**
     * @param class-string|null $dtoFullyQualifiedClassName
     * @param class-string[] $extendedClasses
     * @param class-string[] $dtoInterfaces
     * @param class-string $errorResponseClass
     * @param class-string $paginationClass
     */
    public function __construct(
        private readonly string $controllerNamespaceTemplate,
        private readonly string $dtoNamespaceTemplate,
        public readonly string $projectDir,
        public readonly string $projectBaseNamespace,
        public readonly string $errorResponseClass,
        public array $extendedClasses = [],
        public array $dtoInterfaces = [],
        public ?string $dtoFullyQualifiedClassName = null,
        public string $paginationClass,
    ) {
    }

    private function replaceVariables(string $template, string $classFullyQualifiedClassName): string
    {
        ['domain' => $domain, 'entity' => $entity] = FQCNHelper::extractDomainAndEntity($classFullyQualifiedClassName);

        $namespace = str_replace('{domain}', $domain, $template);
        return str_replace('{entity}', $entity, $namespace);
    }

    public function getDtoPath(string $classFullyQualifiedClassName): string
    {
        return FQCNHelper::convertNamespaceToFilepath(
            $this->replaceVariables($this->dtoNamespaceTemplate, $classFullyQualifiedClassName),
            $this->projectBaseNamespace,
            $this->projectDir
        );
    }

    public function getControllerPath(string $classFullyQualifiedClassName): string
    {
        return FQCNHelper::convertNamespaceToFilepath(
            $this->replaceVariables($this->controllerNamespaceTemplate, $classFullyQualifiedClassName),
            $this->projectBaseNamespace,
            $this->projectDir
        );
    }

    public function getDtoNamespace(string $classFullyQualifiedClassName): string
    {
        return $this->replaceVariables($this->dtoNamespaceTemplate, $classFullyQualifiedClassName);
    }

    public function getControllerNamespace(string $classFullyQualifiedClassName): string
    {
        return $this->replaceVariables($this->controllerNamespaceTemplate, $classFullyQualifiedClassName);
    }

    /**
     * @return class-string[]
     */
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

    /** @return class-string */
    public function getPaginationClass(): string
    {
        return $this->paginationClass;
    }
}
