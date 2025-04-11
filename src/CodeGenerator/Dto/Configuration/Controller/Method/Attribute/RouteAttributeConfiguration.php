<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\Attribute;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\IMethodAttributeConfiguration;

readonly class RouteAttributeConfiguration implements IMethodAttributeConfiguration
{
    /**
     * @param string[] $methods
     */
    public function __construct(
        public string $path,
        public string $name,
        public array $methods,
    ) {
    }

    public function render(): string
    {
        $methods = "'" . implode("', '", $this->methods) . "'";
        return "#[Route('{$this->path}', name: '{$this->name}', methods: [{$methods}])]";
    }

    public function getIdentifier(): string
    {
        return self::class . '_' . $this->path;
    }
}
