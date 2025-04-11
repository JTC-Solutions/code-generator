<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;

interface IClassWriter
{
    /**
     * @param array<string, mixed> $data
     */
    public function write(
        Context $context,
        string $className,
        string $code,
        array $data = [],
    ): void;
}
