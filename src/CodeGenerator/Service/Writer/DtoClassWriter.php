<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use PhpParser\Error;
use PhpParser\ParserFactory;

class DtoClassWriter extends BaseClassWriter implements IClassWriter
{
    public function write(
        Context $context,
        string $className,
        string $code,
    ): string {
        $filepath = sprintf('%s/%s.php', $context->dtoPath, $className);

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        try {
            $parser->parse($code);
        } catch (Error $e) {
            throw TemplateNotValidPhpCodeException::create($className, $context->entityFQCN, $e);
        }

        $this->dumpFile($filepath, $code);

        return $filepath;
    }
}
