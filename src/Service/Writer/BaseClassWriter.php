<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use RuntimeException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

abstract class BaseClassWriter
{
    protected readonly Filesystem $filesystem;

    protected readonly Parser $parser;

    public function __construct(
        protected readonly ContextProvider $contextProvider,
        protected readonly ParserFactory $parserFactory,
    ) {
        $this->parser = $this->parserFactory->createForNewestSupportedVersion();
        $this->filesystem = new Filesystem();
    }

    protected function dumpFile(string $filepath, string $code): void
    {
        try {
            $this->filesystem->dumpFile($filepath, $code);
        } catch (IOExceptionInterface $e) {
            throw new RuntimeException(sprintf('Failed to write file "%s": %s', $filepath, $e->getMessage()), 0, $e);
        }
    }
}
