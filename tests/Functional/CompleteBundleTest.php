<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

use JtcSolutions\CodeGenerator\Command\GenerateCrudCommand;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\CreateControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\DeleteControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\DetailControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\ListControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Controller\UpdateControllerGenerator;
use JtcSolutions\CodeGenerator\Tests\Functional\TestEntityClass\TestEntityClass;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CompleteBundleTest extends BaseFunctionalTest
{
    public function testSuccess(): void
    {
        $app = new Application();
        $app->addCommands([new GenerateCrudCommand(
            controllerGenerators: [
                $this->createCreateControllerGenerator(),
                $this->createDeleteControllerGenerator(),
                $this->createUpdateControllerGenerator(),
                $this->createDetailControllerGenerator(),
                $this->createListControllerGenerator(),
            ],
            serviceGenerator: $this->createServiceGenerator(),
            contextProvider: $this->createContextProvider(),
            dtoGenerator: $this->createDtoGenerator(),
            repositoryGenerator: $this->createRepositoryGenerator(),
        )]);
        $command = $app->find('jtc-solutions:generate-crud');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            input: [
                'targetClass' => TestEntityClass::class,
                'with-service' => true,
            ],
        );

        $controllerGenerators = [
            'CreateTestEntityClassController' => CreateControllerGenerator::class,
            'UpdateTestEntityClassController' => UpdateControllerGenerator::class,
            'DeleteTestEntityClassController' => DeleteControllerGenerator::class,
            'ListTestEntityClassController' => ListControllerGenerator::class,
            'DetailTestEntityClassController' => DetailControllerGenerator::class,
        ];
        $output = $commandTester->getDisplay();

        foreach ($controllerGenerators as $controllerClass => $generator) {
            self::assertStringContainsString(
                needle: sprintf('%s ran', FQCNHelper::transformFQCNToShortClassName($generator)),
                haystack: $output,
            );
            self::assertFileExists(sprintf(__DIR__ . '/../../output/CodeGenerator/App/Api/TestEntityClass/%s.php', $controllerClass));
        }

        // ensure service is created
        self::assertStringContainsString(
            needle: 'Service generated successfully.',
            haystack: $output,
        );
        self::assertFileExists(__DIR__ . '/../../output/CodeGenerator/Domain/Service/TestEntityClassService.php');
    }
}
