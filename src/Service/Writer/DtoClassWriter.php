<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use PhpParser\Error;

class DtoClassWriter extends BaseClassWriter
{
    /**
     * @param class-string $classFullyQualifiedClassName
     * @param class-string $dtoFullyQualifiedClassName
     */
    public function write(
        string $classFullyQualifiedClassName,
        string $dtoFullyQualifiedClassName,
        string $code,
    ): string {
        $dtoClassName = FQCNHelper::transformFQCNToShortClassName($dtoFullyQualifiedClassName);

        $filepath = sprintf('%s/%s.php', $this->contextProvider->getDtoPath($classFullyQualifiedClassName), $dtoClassName);

        try {
            $this->parser->parse($code);
        } catch (Error $e) {
            throw TemplateNotValidPhpCodeException::create($dtoClassName, $classFullyQualifiedClassName, $e);
        }

        $this->dumpFile($filepath, $code);

        return $filepath;
    }
}
