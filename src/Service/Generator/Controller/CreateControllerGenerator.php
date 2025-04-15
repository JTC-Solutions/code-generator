<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\Service\Configurator\Controller\CreateControllerConfigurator;
use JtcSolutions\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class CreateControllerGenerator extends BaseControllerGenerator
{
    protected const string DTO_SUFFIX = 'CreateRequest';

    public function __construct(
        protected readonly DtoGenerator $dtoGenerator,
        protected readonly ContextProvider $contextProvider,
        protected readonly CreateControllerConfigurator $configurator,
    ) {
    }


    public function generate(string $classFullyQualifiedClassName): void
    {
        $this->dtoGenerator->generate($classFullyQualifiedClassName, '', static::DTO_SUFFIX);
        $dtoNamespace = $this->contextProvider->getDtoNamespace();
        $dtoNamespace = str_replace('.php', '', $dtoNamespace);

        // TODO: pass additional namespaces ?

        parent::generate($classFullyQualifiedClassName);
    }

    protected function createConfigurator(): CreateControllerConfigurator
    {
        return new CreateControllerConfigurator();
    }
}
