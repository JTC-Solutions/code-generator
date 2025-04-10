<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Exception;

use Exception;

class InvalidArgumentException extends Exception
{
    public static function unableToExtractDomainName(string $className): self
    {
        return new self(sprintf('Domain name could not be extracted from %s', $className));
    }

    public static function unableToExtractEntityName(string $className): self
    {
        return new self(sprintf('Entity name could not be extracted from %s', $className));
    }
}
