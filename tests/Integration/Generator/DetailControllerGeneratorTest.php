<?php

namespace JtcSolutions\CodeGenerator\Tests\Integration\Generator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\DomainContext;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\ClassWriter;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\DetailControllerConfigurator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\DetailControllerGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DetailControllerGeneratorTest extends TestCase
{
    private DetailControllerGenerator $generator;
    private DetailControllerConfigurator|MockObject $configuratorMock;
    private ControllerCodeRenderer|MockObject $rendererMock;
    private ClassWriter|MockObject $writerMock;
    private DomainContext $context;

    protected function setUp(): void
    {
        $this->configuratorMock = $this->createMock(DetailControllerConfigurator::class);
        $this->rendererMock = $this->createMock(ControllerCodeRenderer::class);
        $this->writerMock = $this->createMock(ClassWriter::class);

        $this->generator = new DetailControllerGenerator();
        $this->context = new DomainContext('TestDomain', 'App\Entity\Test');

        // Mock the renderer creation
        $mockedRenderer = $this->mockControllerCodeRenderer();
        $this->generator = $mockedRenderer;
    }

    public function testGenerateCreatesAndSavesControllerCode(): void
    {
        // Mock configuration
        $methodConfig = new MethodConfiguration('', '', '', [], []);
        $controllerConfig = new ControllerConfiguration(
            'TestController',
            'App\Test',
            $methodConfig,
            [],
            [],
            [],
            [],
            [],
            null
        );

        $this->configuratorMock->expects($this->once())
            ->method('configure')
            ->with('TestDomain', 'App\Entity\Test')
            ->willReturn($controllerConfig);

        // Mock rendering
        $this->rendererMock->expects($this->once())
            ->method('generateCode')
            ->willReturn('<?php // Generated code');

        // Mock writing
        $this->writerMock->expects($this->once())
            ->method('write')
            ->with('<?php // Generated code');

        // Execute
        $this->generator->generate($this->context);
    }

    private function mockControllerCodeRenderer(): DetailControllerGenerator
    {
        $generator = $this->getMockBuilder(DetailControllerGenerator::class)
            ->onlyMethods(['createConfigurator', 'createCodeRenderer', 'createClassWriter'])
            ->getMock();

        $generator->method('createConfigurator')
            ->willReturn($this->configuratorMock);

        $generator->method('createCodeRenderer')
            ->willReturn($this->rendererMock);

        $generator->method('createClassWriter')
            ->willReturn($this->writerMock);

        return $generator;
    }
}