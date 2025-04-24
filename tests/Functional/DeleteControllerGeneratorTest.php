<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

class DeleteControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGenerationDelete(): void
    {
        $generator = $this->createDeleteControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/CodeGenerator/App/Api/TestEntityClass/DeleteTestEntityClassController.php');
    }
}
