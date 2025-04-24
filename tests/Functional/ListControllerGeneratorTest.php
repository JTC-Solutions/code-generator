<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

class ListControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGeneration(): void
    {
        $generator = $this->createListControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/CodeGenerator/App/Api/TestEntityClass/ListTestEntityClassController.php');
    }
}
