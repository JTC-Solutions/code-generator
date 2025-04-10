<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service;

class ClassWriter
{
    public function write(string $code): void
    {
        $destination = 'test.php';

        file_put_contents($destination, $code);
    }
}
