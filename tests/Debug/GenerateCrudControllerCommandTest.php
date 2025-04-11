<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Debug;

use JtcSolutions\CodeGenerator\CodeGenerator\Command\GenerateCrudControllerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateCrudControllerCommandTest extends TestCase
{
    public function testBasic(): void
    {
        self::markTestSkipped();
        $app = new Application();
        $generateCrudControllerCommand = new GenerateCrudControllerCommand();
        $app->addCommands([$generateCrudControllerCommand]);
        $command = $app->find('app:test');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'controllerNamespace' => 'App\Output\App\Api\User',
            'dtoNamespace' => 'App\Output\Domain\Dto\User',
            'targetClass' => 'App\Contact\Domain\Entity\User',
        ]);

        $commandTester->assertCommandIsSuccessful();
    }
}
