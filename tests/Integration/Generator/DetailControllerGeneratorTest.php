<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Integration\Generator;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\ControllerConfiguration\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\ControllerCodeRenderer;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\DetailControllerConfigurator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\DetailControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Writer\ControllerClassWriter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class DetailControllerGeneratorTest extends TestCase
{
    private const string OUTPUT_PATH = __DIR__ . '/output';

    private DetailControllerGenerator $generator;

    private DetailControllerConfigurator|MockObject $configuratorMock;

    private ControllerCodeRenderer|MockObject $rendererMock;

    private ControllerClassWriter|MockObject $writerMock;

    private Context $context;

    protected function setUp(): void
    {
        $this->configuratorMock = $this->createMock(DetailControllerConfigurator::class);
        $this->rendererMock = $this->createMock(ControllerCodeRenderer::class);
        $this->writerMock = $this->createMock(ControllerClassWriter::class);

        $this->generator = new DetailControllerGenerator();
        $this->context = new Context('TestDomain', 'App\Entity\Test', self::OUTPUT_PATH);

        // Mock the renderer creation
        $mockedRenderer = $this->mockControllerCodeRenderer();
        $this->generator = $mockedRenderer;
    }

    protected function tearDown(): void
    {
        $finder = Finder::create();
        $finder->in(self::OUTPUT_PATH);

        foreach ($finder->files() as $file) {
            unset($file);
        }
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
            null,
        );

        $this->configuratorMock->expects(self::once())
            ->method('configure')
            ->with(new Context('TestDomain', 'App\Entity\Test', self::OUTPUT_PATH))
            ->willReturn($controllerConfig);

        // Mock rendering
        $this->rendererMock->expects(self::once())
            ->method('generateCode')
            ->willReturn('<?php // Generated code');

        // Mock writing
        $this->writerMock->expects(self::once())
            ->method('write');

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
