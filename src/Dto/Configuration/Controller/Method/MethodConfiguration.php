<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

/**
 * Data Transfer Object representing the configuration for a single method
 * within a class (e.g., a controller action or a service method) to be generated.
 *
 * It holds details like the method's name, return type, body content, arguments,
 * and any PHP attributes associated with it.
 */
readonly class MethodConfiguration implements IConfiguration
{
    /**
     * Constructs a new MethodConfiguration instance.
     *
     * @param string $name The name of the method (e.g., 'index', 'create', 'getUser').
     * @param string $returnType The PHP return type declaration for the method (e.g., 'void', 'JsonResponse', 'App\Entity\User', '?string').
     * @param string $methodBody The string containing the actual code to be placed inside the method's curly braces.
     * @param array<int, MethodArgumentConfiguration> $arguments An array of MethodArgumentConfiguration objects, each defining an argument for this method.
     * @param array<int, IMethodAttributeConfiguration> $attributes An array of configuration objects (implementing IMethodAttributeConfiguration) representing PHP attributes (like #[Route], #[OA\Post]) to be rendered above the method definition.
     */
    public function __construct(
        public string $name,
        public string $returnType,
        public string $methodBody,
        public array $arguments,
        public array $attributes,
    ) {
    }

    /**
     * Provides a unique identifier for this method configuration, typically the method name.
     * This might be used for tracking or referencing during the code generation process.
     *
     * @return string The name of the method.
     */
    public function getIdentifier(): string
    {
        return $this->name;
    }
}
