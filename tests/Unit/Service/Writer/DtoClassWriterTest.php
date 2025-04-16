<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Unit\Service\Writer;

use JtcSolutions\CodeGenerator\Exception\TemplateNotValidPhpCodeException;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\CodeGenerator\Service\Writer\Dto\DtoClassWriter;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class DtoClassWriterTest extends TestCase
{
    private MockObject&ContextProvider $contextProviderMock;

    private DtoClassWriter $writer;

    private vfsStreamDirectory $root;

    private string $entityFqcn = 'App\Domain\Test\Entity\MyEntity';

    private string $dtoFqcn = 'App\Application\Dto\Test\MyEntity\MyDto';

    protected function setUp(): void
    {
        $this->contextProviderMock = $this->createMock(ContextProvider::class);
        $this->writer = new DtoClassWriter($this->contextProviderMock);
        $this->root = vfsStream::setup('projectRoot');
    }

    public function testWriteSuccess(): void
    {
        $dtoPath = vfsStream::url('projectRoot/Dto/Test/MyEntity');
        $expectedFilePath = $dtoPath . '/MyDto.php';
        $code = '<?php namespace App\Application\Dto\Test\MyEntity; class MyDto {}';

        $this->contextProviderMock->expects(self::once())
            ->method('getDtoPath')
            ->with($this->entityFqcn)
            ->willReturn($dtoPath);

        $resultPath = $this->writer->write($this->entityFqcn, $this->dtoFqcn, $code);

        self::assertSame($expectedFilePath, $resultPath);
        self::assertTrue($this->root->hasChild('Dto/Test/MyEntity/MyDto.php'));
        self::assertSame($code, file_get_contents($expectedFilePath));
    }

    public function testWriteCreatesDirectory(): void
    {
        $dtoPath = vfsStream::url('projectRoot/New/Path/To/Dto');
        $expectedFilePath = $dtoPath . '/MyDto.php';
        $code = '<?php namespace App\Application\Dto\Test\MyEntity; class MyDto {}';

        $this->contextProviderMock->expects(self::once())
            ->method('getDtoPath')
            ->with($this->entityFqcn)
            ->willReturn($dtoPath);

        self::assertFalse($this->root->hasChild('New/Path/To/Dto'));

        $this->writer->write($this->entityFqcn, $this->dtoFqcn, $code);

        self::assertTrue($this->root->hasChild('New/Path/To/Dto/MyDto.php'));
        self::assertSame($code, file_get_contents($expectedFilePath));
    }

    public function testWriteInvalidPhpThrowsException(): void
    {
        $this->expectException(TemplateNotValidPhpCodeException::class);
        $this->expectExceptionMessage('Unable to generate code for App\Domain\Test\Entity\MyEntity because the provided template is invalid for file MyDto');

        $dtoPath = vfsStream::url('projectRoot/Dto');
        $code = '<?php this is not valid php';

        $this->contextProviderMock->expects(self::once())
            ->method('getDtoPath')
            ->with($this->entityFqcn)
            ->willReturn($dtoPath);

        $this->writer->write($this->entityFqcn, $this->dtoFqcn, $code);
    }

    public function testWriteIOException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to write file');

        $dtoPath = vfsStream::url('projectRoot/Dto');
        $code = '<?php namespace App\Application\Dto\Test\MyEntity; class MyDto {}';

        $this->contextProviderMock->expects(self::once())
            ->method('getDtoPath')
            ->with($this->entityFqcn)
            ->willReturn($dtoPath);

        // Make the directory unwritable after setup
        vfsStream::newDirectory('Dto', 0000)->at($this->root); // Readonly

        $this->writer->write($this->entityFqcn, $this->dtoFqcn, $code);
    }
}
