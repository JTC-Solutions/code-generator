<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\IConfiguration;

readonly class MethodArgumentConfiguration implements IConfiguration
{
    public function __construct(
        public string $argumentName,
        public string $argumentType,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class . '_' . $this->argumentName;
    }
}
