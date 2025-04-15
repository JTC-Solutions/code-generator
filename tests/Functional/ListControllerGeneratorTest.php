<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\Configurator\Controller\ListControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\ListControllerGenerator;

class ListControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGeneration(): void
    {
        $generator = $this->createListControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/DomainName/App/Api/EntityName/ListEntityClassController.php');
    }

    protected function createListControllerGenerator(): ListControllerGenerator
    {
        return new ListControllerGenerator(
            configurator: $this->createListControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createListControllerConfigurator(): ListControllerConfigurator
    {
        return new ListControllerConfigurator(
            contextProvider: $this->createContextProvider(),
        );
    }
}
