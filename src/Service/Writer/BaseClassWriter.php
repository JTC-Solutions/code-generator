<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use RuntimeException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Abstract base class for class writers.
 * Provides common dependencies like Filesystem and PHP Parser,
 * and a helper method for dumping files.
 */
abstract class BaseClassWriter
{
    /**
     * @var Filesystem Symfony Filesystem component instance.
     */
    protected readonly Filesystem $filesystem;

    /**
     * @var Parser PHP Parser instance for code validation.
     */
    protected readonly Parser $parser;

    /**
     * @param ContextProvider $contextProvider Provides context needed to determine file paths.
     */
    public function __construct(
        protected readonly ContextProvider $contextProvider,
    ) {
        $parserFactory = new ParserFactory();
        $this->parser = $parserFactory->createForNewestSupportedVersion();
        $this->filesystem = new Filesystem();
    }

    /**
     * Dumps the provided code content into the specified file path.
     * Creates necessary directories if they don't exist.
     * Wraps Filesystem exceptions in a RuntimeException.
     *
     * @param string $filepath The absolute path to the file to write.
     * @param string $code The code content to write.
     * @throws RuntimeException If the file cannot be written due to I/O errors (permissions, disk space, etc.).
     */
    protected function dumpFile(string $filepath, string $code): void
    {
        try {
            $this->filesystem->dumpFile($filepath, $code);
        } catch (IOExceptionInterface $e) {
            throw new RuntimeException(sprintf('Failed to write file "%s": %s', $filepath, $e->getMessage()), 0, $e);
        }
    }
}
