<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\OpenApiDoc\IOpenApiDocConfiguration;

readonly class ControllerConfiguration
{
    /**
     * @param array<int, string> $extends
     * @param array<int, string> $useStatements
     * @param array<int, IOpenApiDocConfiguration> $openApiDocs
     * @param array<int, string> $interfaces
     * @param array<int, MethodArgumentConfiguration> $constructorParams
     */
    public function __construct(
        public string $className,
        public string $namespace,
        public ?MethodConfiguration $methodConfiguration,
        public array $extends = [],
        public array $useStatements = [],
        public array $openApiDocs = [],
        public array $interfaces = [],
        public array $constructorParams = [],
        public ?string $constructorBody = null,
    ) {
    }
}
