<?php
declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\Configurator\Controller\CreateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\CreateControllerGenerator;

class CreateControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGeneration(): void
    {
        $generator = $this->createCreateControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/DomainName/App/Api/EntityName/CreateEntityNameController.php');
    }

    protected function createCreateControllerGenerator(): CreateControllerGenerator
    {
        return new CreateControllerGenerator(
            dtoGenerator: $this->createDtoGenerator(),
            contextProvider: $this->createContextProvider(),
            configurator: $this->createCreateControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createCreateControllerConfigurator(): CreateControllerConfigurator
    {
        return new CreateControllerConfigurator(
            contextProvider: $this->createContextProvider(),
        );
    }
}
