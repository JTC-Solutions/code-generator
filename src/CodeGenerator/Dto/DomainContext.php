<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto;

readonly class DomainContext
{
    public function __construct(
        public string $domain,
        public string $entity,
    ) {
    }
}
