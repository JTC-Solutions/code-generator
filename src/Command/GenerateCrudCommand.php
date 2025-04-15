<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Command;

use JtcSolutions\CodeGenerator\Service\Generator\Controller\BaseControllerGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate CRUD Controllers for given class. Works only on Domain Driven Design architecture.')
            ->addArgument('targetClass', InputArgument::REQUIRED, 'Target class for which to generate.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var class-string|null $targetClass */
        $targetClass = $input->getArgument('targetClass');

        if ($targetClass === null) {
            return Command::INVALID;
        }

        foreach ($this->controllerGenerators as $controllerGenerator) {
            $controllerGenerator->generate($targetClass);
        }

        return Command::SUCCESS;
    }
}
