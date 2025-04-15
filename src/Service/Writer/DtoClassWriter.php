<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use PhpParser\Error;

class DtoClassWriter extends BaseClassWriter implements IClassWriter
{
    public function write(
        string $classFullyQualifiedClassName,
        string $className,
        string $code,
    ): string {
        $filepath = sprintf('%s/%s.php', $this->contextProvider->getDtoPath($classFullyQualifiedClassName), $className);

        try {
            $this->parser->parse($code);
        } catch (Error $e) {
            throw TemplateNotValidPhpCodeException::create($className, $classFullyQualifiedClassName, $e);
        }

        $this->dumpFile($filepath, $code);

        return $filepath;
    }
}
