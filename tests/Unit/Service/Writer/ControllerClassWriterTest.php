<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Writer;

use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\Writer\ControllerClassWriter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpParser\Error as ParserError;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

// Import vfsStream
// Import vfsStreamDirectory
// Import PhpParser\Error

class ControllerClassWriterTest extends TestCase
{
    private ControllerClassWriter $writer;

    private vfsStreamDirectory $root;

    private Context $context;

    protected function setUp(): void
    {
        $this->writer = new ControllerClassWriter();
        $this->root = vfsStream::setup('projectDir'); // Setup virtual file system

        $this->context = new Context(
            entityFQCN: 'App\Entity\Test',
            entityNamespace: 'App\Entity',
            entityPath: 'vfs://projectDir/Entity', // Use vfs path
            controllerPath: 'vfs://projectDir/Controller', // Use vfs path
            controllerNamespace: 'App\Controller',
            dtoPath: 'vfs://projectDir/Dto', // Use vfs path
            dtoNamespace: 'App\Dto',
            errorResponseClass: Exception::class,
        );

        // Ensure the target directory exists in VFS
        vfsStream::newDirectory('Controller')->at($this->root);
    }

    public function testWriteValidCode(): void
    {
        $className = 'MyValidController';
        $code = "<?php namespace App\Controller; class {$className} {}";
        $expectedPath = 'vfs://projectDir/Controller/' . $className . '.php';

        $this->writer->write($this->context, $className, $code);

        self::assertTrue($this->root->hasChild('Controller/' . $className . '.php'));
        self::assertEquals($code, file_get_contents($expectedPath));
    }

    public function testWriteInvalidCodeThrowsException(): void
    {
        $className = 'MyInvalidController';
        $code = '<?php namespace App\Controller; class Invalid { {}'; // Invalid PHP

        $this->expectException(TemplateNotValidPhpCodeException::class); //
        $this->expectExceptionMessageMatches(
            '/Unable to generate code for App\\\\Entity\\\\Test because the provided template is invalid for file MyInvalidController/',
        );

        try {
            $this->writer->write($this->context, $className, $code);
        } catch (TemplateNotValidPhpCodeException $e) {
            self::assertInstanceOf(ParserError::class, $e->getPrevious()); // Check underlying cause
            self::assertFalse($this->root->hasChild('Controller/' . $className . '.php')); // File should not be created
            throw $e;
        } catch (Throwable $e) {
            self::fail('Unexpected exception type thrown: ' . get_class($e));
        }
    }
}
