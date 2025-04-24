<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Command;

use JtcSolutions\CodeGenerator\Service\Generator\Controller\BaseControllerGenerator;
use JtcSolutions\CodeGenerator\Service\Generator\Service\ServiceGenerator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Core\Entity\IEntity;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand('jtc-solutions:generate-crud')]
class GenerateCrudCommand extends Command
{
    /**
     * @param BaseControllerGenerator[] $controllerGenerators
     */
    public function __construct(
        #[AutowireIterator('jtc_solutions.controller_generator')]
        private readonly iterable $controllerGenerators,
        private readonly ServiceGenerator $serviceGenerator,
        private readonly ContextProvider $contextProvider,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate CRUD Controllers for given class. Works only on Domain Driven Design architecture.')
            ->addArgument('targetClass', InputArgument::REQUIRED, 'Target class for which to generate.')
            ->addArgument('with-service', InputArgument::OPTIONAL, 'Generate service for the target class.', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var class-string<IEntity>|null $targetClass */
        $targetClass = $input->getArgument('targetClass');

        /** @var bool $withService */
        $withService = $input->getArgument('with-service');

        if ($targetClass === null) {
            return Command::INVALID;
        }

        $io = new SymfonyStyle($input, $output);

        if ($withService === true) {
            $serviceFullyQualifiedClassName = $this->serviceGenerator->generate($targetClass);
            $this->contextProvider->serviceFullyQualifiedClassName = $serviceFullyQualifiedClassName;
            $io->success('Service generated successfully.');
        }

        foreach ($this->controllerGenerators as $controllerGenerator) {
            $controllerGenerator->generate($targetClass);
            $io->success(sprintf('Controller Generator %s ran successfully.', $controllerGenerator::class));
        }

        return Command::SUCCESS;
    }
}
