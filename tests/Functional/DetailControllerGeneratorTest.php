<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

class DetailControllerGeneratorTest extends BaseFunctionalTest
{
    public function testSuccessfulCompleteGeneration(): void
    {
        $generator = $this->createDetailControllerGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/CodeGenerator/App/Api/TestEntityClass/DetailTestEntityClassController.php');
    }
}
