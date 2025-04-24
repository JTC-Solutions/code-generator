<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\IMethodAttributeConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;

/**
 * @extends BaseConfigurationBuilder<MethodConfiguration>
 * Builder for creating MethodConfiguration objects.
 * Provides methods to define the method's name, return type, body, attributes, and arguments.
 */
class MethodConfigurationBuilder extends BaseConfigurationBuilder
{
    /**
     * @const string Type identifier for method attributes.
     */
    protected const string METHOD_ATTRIBUTE = 'methodAttribute';

    /**
     * @const string Type identifier for method arguments.
     */
    protected const string METHOD_ARGUMENT = 'methodArgument';

    /**
     * @var array<int, MethodArgumentConfiguration> Stores method argument configurations, keyed by order index.
     */
    public array $arguments = [];

    /**
     * @var array<int, IMethodAttributeConfiguration> Stores method attribute configurations, keyed by order index.
     */
    public array $attributes = [];

    /**
     * @param string $name The name of the method.
     * @param string $returnType The return type hint for the method (e.g., 'JsonResponse', 'void', '?string').
     * @param string $body The code content for the method body.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $returnType,
        public readonly string $body,
    ) {
    }

    /**
     * Builds the final MethodConfiguration object.
     * Sorts collected arguments and attributes by their order index.
     *
     * @return MethodConfiguration The fully configured method DTO.
     */
    public function build(): MethodConfiguration
    {
        ksort($this->arguments);
        ksort($this->attributes);

        return new MethodConfiguration(
            name: $this->name,
            returnType: $this->returnType,
            methodBody: $this->body,
            arguments: $this->arguments,
            attributes: $this->attributes,
        );
    }

    /**
     * Adds an attribute (e.g., #[Route], #[OA\Response]) to the method configuration.
     *
     * @param IMethodAttributeConfiguration $attribute The attribute configuration object.
     * @param int|null $order Optional order index for the attribute.
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the attribute (by identifier) already exists or the order index is taken.
     */
    public function addAttribute(IMethodAttributeConfiguration $attribute, ?int $order = null): self
    {
        /** @var array<int,IMethodAttributeConfiguration> $result */
        $result = $this->addItem(self::METHOD_ATTRIBUTE, $attribute, $this->attributes, $order);

        $this->attributes = $result;

        return $this;
    }

    /**
     * Adds an argument to the method signature configuration.
     * Note: Use statements for argument type hints should be handled by the ControllerConfigurationBuilder.
     *
     * @param MethodArgumentConfiguration $argument The argument configuration object (should use short type name).
     * @param int|null $order Optional order index for the argument.
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the argument (by name) already exists or the order index is taken.
     */
    public function addArgument(MethodArgumentConfiguration $argument, ?int $order = null): self
    {
        /** @var array<int,MethodArgumentConfiguration> $result */
        $result = $this->addItem(self::METHOD_ARGUMENT, $argument, $this->arguments, $order);

        $this->arguments = $result;

        return $this;
    }
}
