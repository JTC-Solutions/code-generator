<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

interface IClassWriter
{
    public function write(
        string $classFullyQualifiedClassName,
        string $className,
        string $code,
    ): string;
}
