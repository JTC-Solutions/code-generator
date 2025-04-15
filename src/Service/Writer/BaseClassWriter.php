<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Writer;

use RuntimeException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

abstract class BaseClassWriter
{
    protected Filesystem $filesystem;

    public function __construct(
    ) {
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
