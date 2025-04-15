<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Service\Configurator\Controller\DeleteControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\DeleteControllerGenerator;

class DeleteControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGenerationDelete(): void
    {
        $generator = $this->createDeleteControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/DomainName/App/Api/EntityName/DeleteEntityNameController.php');
    }

    protected function createDeleteControllerGenerator(): DeleteControllerGenerator
    {
        return new DeleteControllerGenerator(
            configurator: $this->createDeleteControllerConfigurator(),
            classWriter: $this->createControllerClassWriter(),
            codeRenderer: $this->createControllerCodeRenderer(),
        );
    }

    protected function createDeleteControllerConfigurator(): DeleteControllerConfigurator
    {
        return new DeleteControllerConfigurator(
            contextProvider: $this->createContextProvider(),
        );
    }
}
