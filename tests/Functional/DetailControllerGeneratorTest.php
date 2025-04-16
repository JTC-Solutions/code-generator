<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DetailControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\DetailControllerGenerator;

class DetailControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGeneration(): void
    {
        $generator = $this->createDetailControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/CodeGenerator/App/Api/TestEntityClass/DetailTestEntityClassController.php');
    }

    protected function createDetailControllerGenerator(): DetailControllerGenerator
    {
        return new DetailControllerGenerator(
            configurator: $this->createDetailControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createDetailControllerConfigurator(): DetailControllerConfigurator
    {
        return new DetailControllerConfigurator(
            contextProvider: $this->createContextProvider(),
        );
    }
}
