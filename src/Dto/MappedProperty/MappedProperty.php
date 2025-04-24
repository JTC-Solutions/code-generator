<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Dto\MappedProperty;

class MappedProperty
{
    /**
     * @param class-string|null $useStatement
     */
    public function __construct(
        public string $name,
        public string $type,
        public ?string $useStatement = null,
        public bool $nullable = false,
    ) {
    }
}
