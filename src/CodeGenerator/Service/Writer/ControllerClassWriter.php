<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use PhpParser\Error;
use PhpParser\ParserFactory;

class ControllerClassWriter implements IControllerClassWriter
{
    public function write(
        Context $context,
        string $className,
        string $code,
    ): void {
        $filepath = sprintf('%s/%s.php', $context->controllerPath, $className);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        try {
            $parser->parse($code);
            file_put_contents($filepath, $code);
        } catch (Error $e) {
            throw TemplateNotValidPhpCodeException::create($className, $context->entityFQCN, $e);
        }
    }
}
