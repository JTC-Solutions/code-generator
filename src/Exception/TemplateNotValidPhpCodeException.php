<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Exception;

use PHPUnit\Framework\Exception;
use Throwable;

class TemplateNotValidPhpCodeException extends Exception
{
    public static function create(
        string $filename,
        string $entity,
        Throwable $parserException,
    ): self {
        return new self(
            sprintf(
                'Unable to generate code for %s because the provided template is invalid for file %s',
                $entity,
                $filename,
            ),
            0,
            $parserException,
        );
    }
}
