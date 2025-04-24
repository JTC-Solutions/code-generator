<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Service\ServiceConfiguration;

/** @extends BaseClassConfigurationBuilder<ServiceConfiguration> */
class ServiceConfigurationBuilder extends BaseClassConfigurationBuilder
{
    /**
     * @param array<int, MethodConfiguration> $methodConfigurations
     */
    public function __construct(
        string $className,
        string $namespace,
        array $useStatements = [],
        array $interfaces = [],
        bool $callParent = true,
        protected array $methodConfigurations = [],
    ) {
        parent::__construct(
            className: $className,
            namespace: $namespace,
            useStatements: $useStatements,
            interfaces: $interfaces,
            callParent: $callParent,
        );
    }

    public function addMethodConfiguration(MethodConfiguration $methodConfiguration): self
    {
        $this->methodConfigurations[] = $methodConfiguration;

        return $this;
    }

    /**
     * @return ServiceConfiguration The fully configured service DTO.
     */
    public function build(): IRenderableConfiguration
    {
        $this->sortArrays();
        ksort($this->methodConfigurations);

        return new ServiceConfiguration(
            className: $this->className,
            namespace: $this->namespace,
            methodConfigurations: $this->methodConfigurations,
            extends: $this->extends,
            useStatements: $this->useStatements,
            interfaces: $this->interfaces,
            constructorParams: $this->constructorParams,
            callParent: $this->callParent,
        );
    }
}
