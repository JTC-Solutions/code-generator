<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method;

readonly class MethodConfiguration
{
    /**
     * @param array<int, MethodArgumentConfiguration> $arguments
     * @param array<int, IMethodAttributeConfiguration> $attributes
     */
    public function __construct(
        public string $name,
        public string $returnType,
        public string $methodBody,
        public array $arguments,
        public array $attributes,
    ) {
    }
}
