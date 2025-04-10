<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Command;

use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\DetailControllerGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider\DomainContextProvider;
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

    protected DomainContextProvider $domainContextProvider;

    protected bool $override;

    protected function configure(): void
    {
        $this
            ->setDescription('Generate CRUD Controllers for given entity in given domain.')
            ->addArgument('domain', InputArgument::REQUIRED, 'Domain used to generate')
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity name for which to generate')
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Override existing controllers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->console = new SymfonyStyle($input, $output);
        $this->loadArguments($input);

        $detailControllerGenerator = new DetailControllerGenerator();
        $detailControllerGenerator->generate($this->domainContextProvider->getContext());

        return Command::SUCCESS;
    }

    private function loadArguments(InputInterface $input): void
    {
        /** @var string|null $entity */
        $entity = $input->getArgument('entity');
        /** @var string|null $domain */
        $domain = $input->getArgument('domain');

        $this->override = (bool) $input->getOption('override');

        $this->domainContextProvider = new DomainContextProvider($domain, $entity);
    }
}
