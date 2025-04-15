<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use PhpParser\Error;
use PhpParser\ParserFactory;

class ControllerClassWriter extends BaseClassWriter implements IControllerClassWriter
{
    public function write(
        string $classFullyQualifiedClassName,
        string $className,
        string $code,
    ): string {
        $filepath = sprintf('%s/%s.php', $this->contextProvider->getControllerPath($classFullyQualifiedClassName), $className);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        try {
            $parser->parse($code);
        } catch (Error $e) {
            throw TemplateNotValidPhpCodeException::create($className, $classFullyQualifiedClassName, $e);
        }

        $this->dumpFile($filepath, $code);

        return $filepath;
    }
}
