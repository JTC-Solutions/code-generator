<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Command;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\BaseControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\CreateControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\DetailControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\ListControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider\ContextProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:test')]
class GenerateCrudControllerCommand extends Command
{
    protected SymfonyStyle $console;

    protected ContextProvider $domainContextProvider;

    protected bool $override;

    protected function configure(): void
    {
        $this
            ->setDescription('Generate CRUD Controllers for given entity in given domain.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path where to put the generated classes')
            ->addArgument('domain', InputArgument::REQUIRED, 'Domain used to generate')
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity name for which to generate')
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Override existing controllers');
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
        ];

        foreach ($generators as $generator) {
            $generator->generate($this->domainContextProvider->getContext());
        }

        return Command::SUCCESS;
    }

    private function loadArguments(InputInterface $input): void
    {
        $path = $input->getArgument('path');

        if (is_string($path) === false) {
            $this->console->error('Path is invalid!');
            return;
        }

        /** @var class-string|null $entity */
        $entity = $input->getArgument('entity');
        /** @var string|null $domain */
        $domain = $input->getArgument('domain');

        $this->override = (bool) $input->getOption('override');

        if ($entity === null || $domain == null) {
            $this->console->error('Domain or Entity parameter is invalid');
            return;
        }

        $this->domainContextProvider = new ContextProvider($path, $domain, $entity);
    }
}
