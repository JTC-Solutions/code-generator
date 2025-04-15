<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration;

abstract readonly class BaseConfiguration
{
    /**
     * @param array<int, UseStatementConfiguration> $useStatements
     * @param array<int, string> $extends
     * @param array<int, string> $interfaces
     */
    public function __construct(
        public string $namespace,
        public string $className,
        public array $useStatements = [],
        public array $extends = [],
        public array $interfaces = [],
    ) {
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return array<int, UseStatementConfiguration>
     */
    public function getUseStatements(): array
    {
        return $this->useStatements;
    }

    /**
     * @return array<int, string>
     */
    public function getExtends(): array
    {
        return $this->extends;
    }

    /**
     * @return array<int, string>
     */
    public function getInterfaces(): array
    {
        return $this->interfaces;
    }
}
