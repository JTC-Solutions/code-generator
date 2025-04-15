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
    ) {
    }

    public function getDtoPath(string $classFullyQualifiedClassName): string
    {
        ['domain' => $domain, 'entity' => $entity] = FQCNHelper::extractDomainAndEntity($classFullyQualifiedClassName);
        $namespace = str_replace('{domain}', $domain, $this->dtoNamespaceTemplate);
        $namespace = str_replace('{entity}', $entity, $namespace);

        return FQCNHelper::convertNamespaceToFilepath($namespace, $this->projectBaseNamespace, $this->projectDir);
    }

    public function getControllerPath(string $classFullyQualifiedClassName): string
    {
        ['domain' => $domain, 'entity' => $entity] = FQCNHelper::extractDomainAndEntity($classFullyQualifiedClassName);
        $namespace = str_replace('{domain}', $domain, $this->controllerNamespaceTemplate);
        $namespace = str_replace('{entity}', $entity, $namespace);

        return FQCNHelper::convertNamespaceToFilepath($namespace, $this->projectBaseNamespace, $this->projectDir);
    }

    public function getDtoNamespace(string $classFullyQualifiedClassName): string
    {
        ['domain' => $domain, 'entity' => $entity] = FQCNHelper::extractDomainAndEntity($classFullyQualifiedClassName);

        return sprintf($this->dtoNamespaceTemplate, $domain, $entity);
    }

    public function getControllerNamespace(string $classFullyQualifiedClassName): string
    {
        ['domain' => $domain, 'entity' => $entity] = FQCNHelper::extractDomainAndEntity($classFullyQualifiedClassName);

        return sprintf($this->controllerNamespaceTemplate, $domain, $entity);
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
}
