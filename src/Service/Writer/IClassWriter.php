<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use Exception;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use RuntimeException;

/**
 * Interface for class writing services.
 * Defines the contract for writing generated PHP code to a file.
 */
interface IClassWriter
{
    /**
     * Writes the generated PHP code to a file.
     * Implementations should determine the correct file path based on context.
     * Implementations should validate the PHP code before writing.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the class the generated code relates to (e.g., the entity FQCN). Used to determine the path.
     * @param class-string $generatedFullyQualifiedClassName The FQCN of the class being written (e.g., the DTO or Controller FQCN). Used for the filename.
     * @param string $code The generated PHP code string to write.
     * @return string The absolute path to the written file.
     * @throws TemplateNotValidPhpCodeException If the provided code string is not valid PHP.
     * @throws RuntimeException If writing the file to the filesystem fails (e.g., permissions, disk space).
     * @throws Exception For other errors like path calculation failure.
     */
    public function write(
        string $classFullyQualifiedClassName,
        string $generatedFullyQualifiedClassName,
        string $code,
    ): string;
}
