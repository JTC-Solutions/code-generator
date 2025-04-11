<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\IConfiguration;

readonly class UseStatementConfiguration implements IConfiguration
{
    public function __construct(
        public string $fqcn,
        public ?string $alias = null,
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->fqcn;
    }
}
