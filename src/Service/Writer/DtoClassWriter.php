<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use PhpParser\Error;
use PhpParser\ParserFactory;

class DtoClassWriter extends BaseClassWriter implements IClassWriter
{
    public function __construct(
        private readonly ContextProvider $contextProvider,
    ) {
        parent::__construct();
    }

    public function write(
        string $classFullyQualifiedClassName,
        string $className,
        string $code,
    ): string {
        $filepath = sprintf('%s/%s.php', $this->contextProvider->getDtoPath(), $className);

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
