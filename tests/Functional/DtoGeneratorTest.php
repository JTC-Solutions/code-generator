<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Functional;

class DtoGeneratorTest extends BaseFunctionalTest
{
    public function testDtoCompleteGeneration(): void
    {
        $generator = $this->createDtoGenerator();

        $generator->generate(self::DEFAULT_CLASS_FQCN);

        self::assertFileExists(__DIR__ . '/../../output/CodeGenerator/Domain/Dto/TestEntityClass/TestEntityClass.php');
    }
}
