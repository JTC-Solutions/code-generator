<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Command;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller\BaseControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller\CreateControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller\DeleteControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller\DetailControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller\ListControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller\UpdateControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider\ContextProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand('app:test')]
class GenerateCrudControllerCommand extends Command
{
    protected SymfonyStyle $console;

    protected ContextProvider $domainContextProvider;

    protected function configure(): void
    {
        $this
            ->setDescription('Generate CRUD Controllers for given entity in given domain.')
            ->addArgument('controllerNamespace', InputArgument::REQUIRED, 'Path where to put the generated controllers')
            ->addArgument('dtoNamespace', InputArgument::REQUIRED, 'Path where to put the generated DTOs')
            ->addArgument('targetClass', InputArgument::REQUIRED, 'Class for which to generate CRUD');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->console = new SymfonyStyle($input, $output);
        $this->loadArguments($input);

        /** @var BaseControllerGenerator[] $generators */
        $generators = [
            new DetailControllerGenerator(),
            new ListControllerGenerator(),
            new CreateControllerGenerator(),
            new UpdateControllerGenerator(),
            new DeleteControllerGenerator(),
        ];

        foreach ($generators as $generator) {
            $generator->generate($this->domainContextProvider->getContext());
        }

        return Command::SUCCESS;
    }

    private function loadArguments(InputInterface $input): void
    {
        $controllerPath = $input->getArgument('controllerNamespace');
        $dtoPath = $input->getArgument('dtoNamespace');

        if (is_string($controllerPath) === false || is_string($dtoPath) === false) {
            $this->console->error('Paths are invalid!');
            return;
        }

        /** @var class-string|null $entity */
        $entity = $input->getArgument('targetClass');

        if ($entity === null) {
            $this->console->error('Domain or Entity parameter is invalid');
            return;
        }

        $this->domainContextProvider = new ContextProvider($controllerPath, $dtoPath, $entity, Throwable::class);
    }
}
