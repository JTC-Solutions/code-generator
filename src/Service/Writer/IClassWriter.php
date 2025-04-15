<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\Dto\Context;

interface IClassWriter
{
    public function write(
        string $classFullyQualifiedClassName,
        string $className,
        string $code,
    ): string;
}
