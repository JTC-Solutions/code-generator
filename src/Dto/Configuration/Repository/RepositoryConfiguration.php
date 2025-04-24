<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Repository;

use JtcSolutions\CodeGenerator\Dto\Configuration\BaseConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;

readonly class RepositoryConfiguration extends BaseConfiguration implements IRenderableConfiguration
{
    /**
     * @param array<int, MethodArgumentConfiguration> $constructorParams
     */
    public function __construct(
        string $className,
        string $namespace,
        array $extends = [],
        array $useStatements = [],
        array $interfaces = [],
        public array $constructorParams = [],
    ) {
        parent::__construct($namespace, $className, $useStatements, $extends, $interfaces);
    }
}
