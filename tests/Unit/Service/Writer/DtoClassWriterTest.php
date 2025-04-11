<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Writer;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer\DtoClassWriter; // Use the updated DtoClassWriter
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PhpParser\Error as ParserError;
use PHPUnit\Framework\TestCase;

class DtoClassWriterTest extends TestCase
{
    private DtoClassWriter $writer;
    private vfsStreamDirectory $root;
    private Context $context;
    private string $baseDtoPath = 'vfs://projectDir/Generated/Dto';
    private string $baseDtoNamespace = 'App\Generated\Dto';

    protected function setUp(): void
    {
        // Setup virtual file system
        $this->root = vfsStream::setup('projectDir');

        // Instantiate the writer (which now creates its own Filesystem instance)
        $this->writer = new DtoClassWriter();

        // Basic context pointing to a non-existent directory initially
        $this->context = new Context(
            entityFQCN: 'App\Entity\SomeEntity',
            entityNamespace: 'App\Entity',
            entityPath: 'vfs://projectDir/Entity',
            controllerPath: 'vfs://projectDir/Controller',
            controllerNamespace: 'App\Controller',
            dtoPath: $this->baseDtoPath . '/SomeEntity', // Path doesn't exist yet
            dtoNamespace: $this->baseDtoNamespace . '\SomeEntity' // Namespace reflects path
        );
    }

    public function testWriteCreatesDirectoryAndFile(): void
    {
        $className = 'SomeEntityDto';
        $code = "<?php declare(strict_types=1);\n\nnamespace {$this->context->dtoNamespace};\n\nclass {$className} {}";
        $expectedPath = $this->context->dtoPath . '/' . $className . '.php'; // vfs://projectDir/Generated/Dto/SomeEntity/SomeEntityDto.php

        // Ensure directory does NOT exist before write
        $this->assertFalse($this->root->hasChild('Generated/Dto/SomeEntity'));

        // Execute the write method
        $returnedPath = $this->writer->write($this->context, $className, $code);

        // Assertions
        $this->assertEquals($expectedPath, $returnedPath);

        // Check directory exists
        $this->assertTrue($this->root->hasChild('Generated/Dto/SomeEntity'), 'Directory Generated/Dto/SomeEntity should have been created.');

        // Check file exists within the directory
        $dtoDir = $this->root->getChild('Generated/Dto/SomeEntity');
        $this->assertInstanceOf(vfsStreamDirectory::class, $dtoDir); // Make sure it's a directory
        $this->assertTrue($dtoDir->hasChild($className . '.php'), "File {$className}.php should exist in the created directory.");

        // Check file content
        $this->assertEquals($code, $dtoDir->getChild($className . '.php')->getContent());
    }

    public function testWriteInvalidCodeThrowsExceptionAndDoesNotWrite(): void
    {
        $className = 'InvalidDto';
        $code = '<?php declare(strict_types=1); namespace Invalid Syntax {'; // Invalid PHP
        $expectedPath = $this->context->dtoPath . '/' . $className . '.php';

        $this->expectException(TemplateNotValidPhpCodeException::class);
        $this->expectExceptionMessageMatches(
            '/Unable to generate code for App\\\\Entity\\\\SomeEntity because the provided template is invalid for file InvalidDto/'
        );

        try {
            $this->writer->write($this->context, $className, $code);
        } catch (TemplateNotValidPhpCodeException $e) {
            $this->assertInstanceOf(ParserError::class, $e->getPrevious());
            // Ensure directory and file were NOT created due to parser error
            $this->assertFalse(
                $this->root->hasChild('Generated/Dto/SomeEntity/' . $className . '.php'),
                'File should not have been created due to parser error.'
            );
            // Check if the directory itself was created (it shouldn't be if parse fails first)
            $this->assertFalse(
                $this->root->hasChild('Generated/Dto/SomeEntity'),
                'Directory should not have been created if parser fails first.'
            );
            throw $e; // Re-throw
        } catch (\Throwable $e) {
            $this->fail('Unexpected exception type thrown: ' . get_class($e));
        }
    }
}