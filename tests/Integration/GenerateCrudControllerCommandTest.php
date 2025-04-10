<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Tests\Integration;

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

        $commandTester->execute(['domain' => 'Contact', 'entity' => 'App\Contact\Domain\Entity\ContactRelation']);

        $commandTester->assertCommandIsSuccessful();
    }
}
