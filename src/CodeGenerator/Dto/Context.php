<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Dto;

readonly class Context
{
    /**
     * @param class-string $entity
     */
    public function __construct(
        public string $domain,
        public string $entity,
        public string $controllerPath,
    ) {
    }
}
