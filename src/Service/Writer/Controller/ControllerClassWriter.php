<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer\Controller;

use Exception;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\Writer\BaseClassWriter;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use PhpParser\Error;
use PhpParser\ParserFactory;
use RuntimeException;

/**
 * Writes generated Controller PHP code to the appropriate file path.
 * Uses ContextProvider to determine the controller path based on the entity FQCN.
 * Validates the PHP code using PHP Parser before writing.
 */
class ControllerClassWriter extends BaseClassWriter implements IControllerClassWriter
{
    /**
     * Writes the generated Controller code to a file.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the related entity.
     * @param class-string $generatedFullyQualifiedClassName The FQCN of the Controller class being written.
     * @param string $code The generated PHP code string for the Controller.
     * @return string The absolute path to the written file.
     * @throws TemplateNotValidPhpCodeException If the provided code string is not valid PHP.
     * @throws RuntimeException If writing the file to the filesystem fails.
     * @throws Exception If path calculation fails in ContextProvider.
     */
    public function write(
        string $classFullyQualifiedClassName,
        string $generatedFullyQualifiedClassName,
        string $code,
    ): string {
        $generatedClassName = FQCNHelper::transformFQCNToShortClassName($generatedFullyQualifiedClassName);
        $filepath = sprintf('%s/%s.php', $this->contextProvider->getControllerPath($classFullyQualifiedClassName), $generatedClassName);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        try {
            $parser->parse($code);
        } catch (Error $e) {
            throw TemplateNotValidPhpCodeException::create($generatedClassName, $classFullyQualifiedClassName, $e);
        }

        $this->dumpFile($filepath, $code);

        return $filepath;
    }
}
