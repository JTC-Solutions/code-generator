<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Dto;

use JtcSolutions\CodeGenerator\Dto\Configuration\BaseConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;

readonly class DtoConfiguration extends BaseConfiguration implements IRenderableConfiguration
{
    /**
     * @param array<int, DtoPropertyConfiguration> $properties
     * @param array<int, UseStatementConfiguration> $useStatements
     * @param array<int, string> $extends
     * @param array<int, string> $interfaces
     */
    public function __construct(
        string $namespace,
        string $className,
        array $useStatements = [],
        array $extends = [],
        array $interfaces = [],
        public array $properties = [],
    ) {
        parent::__construct($namespace, $className, $useStatements, $extends, $interfaces);
    }

    /**
     * @return array<int, DtoPropertyConfiguration>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
