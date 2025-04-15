<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\Configuration\Dto;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

readonly class DtoPropertyConfiguration implements IConfiguration
{
    public function __construct(
        public string $propertyName,
        public string $propertyType,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class . '_' . $this->propertyName;
    }
}
