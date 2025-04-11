<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

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
