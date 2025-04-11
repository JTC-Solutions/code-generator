<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\BaseConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\IOpenApiDocConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\UseStatementConfiguration;

readonly class ControllerConfiguration extends BaseConfiguration implements IRenderableConfiguration
{
    /**
     * @param array<int, string> $extends
     * @param array<int, UseStatementConfiguration> $useStatements
     * @param array<int, IOpenApiDocConfiguration> $openApiDocs
     * @param array<int, string> $interfaces
     * @param array<int, MethodArgumentConfiguration> $constructorParams
     */
    public function __construct(
        string $className,
        string $namespace,
        public ?MethodConfiguration $methodConfiguration,
        array $extends = [],
        array $useStatements = [],
        public array $openApiDocs = [],
        array $interfaces = [],
        public array $constructorParams = [],
        public ?string $constructorBody = null,
    ) {
        parent::__construct($namespace, $className, $useStatements, $extends, $interfaces);
    }
}
