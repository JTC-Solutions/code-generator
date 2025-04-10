<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\IMethodAttributeConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;

class MethodConfigurationBuilder extends BaseConfigurationBuilder
{
    protected const string METHOD_ATTRIBUTE = 'methodAttribute';

    protected const string METHOD_ARGUMENT = 'methodArgument';

    /**
     * @var array<int, MethodArgumentConfiguration>
     */
    public array $arguments = [];

    /**
     * @var array<int, IMethodAttributeConfiguration>
     */
    public array $attributes = [];

    public function __construct(
        public readonly string $name,
        public readonly string $returnType,
        public readonly string $body,
    ) {
    }

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
     * @throws ConfigurationException
     */
    public function addAttribute(IMethodAttributeConfiguration $attribute, ?int $order = null): self
    {
        /** @var array<int,IMethodAttributeConfiguration> $result */
        $result = $this->addItem(self::METHOD_ATTRIBUTE, $attribute, $this->attributes, $order);

        $this->attributes = $result;

        return $this;
    }

    /**
     * @throws ConfigurationException
     */
    public function addArgument(MethodArgumentConfiguration $argument, ?int $order = null): self
    {
        /** @var array<int,MethodArgumentConfiguration> $result */
        $result = $this->addItem(self::METHOD_ARGUMENT, $argument, $this->arguments, $order);

        $this->arguments = $result;

        return $this;
    }
}
