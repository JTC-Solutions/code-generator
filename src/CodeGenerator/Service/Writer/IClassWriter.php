<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;

interface IClassWriter
{
    public function write(
        Context $context,
        string $className,
        string $code,
    ): string;
}
