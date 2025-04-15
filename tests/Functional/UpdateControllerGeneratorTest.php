<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\Configurator\Controller\UpdateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\UpdateControllerGenerator;

class UpdateControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGeneration(): void
    {
        $generator = $this->createUpdateControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/CodeGenerator/App/Api/TestEntityClass/UpdateTestEntityClassController.php');
    }

    protected function createUpdateControllerGenerator(): UpdateControllerGenerator
    {
        return new UpdateControllerGenerator(
            dtoGenerator: $this->createDtoGenerator(),
            contextProvider: $this->createContextProvider(),
            configurator: $this->createUpdateControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createUpdateControllerConfigurator(): UpdateControllerConfigurator
    {
        return new UpdateControllerConfigurator(
            contextProvider: $this->createContextProvider(),
        );
    }
}
