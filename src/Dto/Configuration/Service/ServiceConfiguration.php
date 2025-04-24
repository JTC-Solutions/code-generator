<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Service;

use JtcSolutions\CodeGenerator\Dto\Configuration\BaseConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;

/**
 * Data Transfer Object holding the configuration required to generate a Service class.
 * Extends BaseConfiguration for common properties like namespace and class name,
 * and implements IRenderableConfiguration, indicating it can be used by a code renderer.
 */
readonly class ServiceConfiguration extends BaseConfiguration implements IRenderableConfiguration
{
    /**
     * Constructs a new ServiceConfiguration instance.
     *
     * @param string $className The short name of the service class to be generated.
     * @param string $namespace The namespace where the service class will reside.
     * @param array<int, MethodConfiguration> $methodConfigurations Configuration objects defining the methods for the service.
     * @param array<int, string> $extends A list of fully qualified class names (FQCNs) that the generated service class should extend. Inherited from BaseConfiguration.
     * @param array<int, UseStatementConfiguration> $useStatements Configuration objects for the 'use' statements required by the service class. Inherited from BaseConfiguration.
     * @param array<int, string> $interfaces A list of FQCNs of interfaces that the generated service class should implement. Inherited from BaseConfiguration.
     * @param array<int, MethodArgumentConfiguration> $constructorParams Configuration objects defining the parameters for the service's constructor.
     * @param ?string $constructorBody Optional string containing custom code to be placed inside the constructor body.
     * @param bool $callParent If true, a call to parent::__construct() will be added to the generated constructor.
     */
    public function __construct(
        string $className,
        string $namespace,
        public array $methodConfigurations,
        array $extends = [],
        array $useStatements = [],
        array $interfaces = [],
        public array $constructorParams = [],
        public ?string $constructorBody = null,
        public bool $callParent = false,
    ) {
        parent::__construct($namespace, $className, $useStatements, $extends, $interfaces);
    }
}
