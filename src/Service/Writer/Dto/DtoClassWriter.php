<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer\Dto;

use Exception;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\Writer\BaseClassWriter;
use JtcSolutions\CodeGenerator\Service\Writer\IClassWriter;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use PhpParser\Error;
use RuntimeException;

/**
 * Writes generated DTO (Data Transfer Object) PHP code to the appropriate file path.
 * Uses ContextProvider to determine the DTO path based on the entity FQCN.
 * Validates the PHP code using PHP Parser before writing.
 */
class DtoClassWriter extends BaseClassWriter implements IClassWriter
{
    /**
     * Writes the generated DTO code to a file.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the related entity (used for path calculation).
     * @param class-string $generatedFullyQualifiedClassName The FQCN of the DTO class being written (used for filename).
     * @param string $code The generated PHP code string for the DTO.
     * @return string The absolute path to the written file.
     * @throws TemplateNotValidPhpCodeException If the provided code string is not valid PHP.
     * @throws RuntimeException If writing the file to the filesystem fails.
     * @throws Exception If path calculation or FQCN parsing fails.
     */
    public function write(
        string $classFullyQualifiedClassName,
        string $generatedFullyQualifiedClassName,
        string $code,
    ): string {
        $dtoClassName = FQCNHelper::transformFQCNToShortClassName($generatedFullyQualifiedClassName);

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
