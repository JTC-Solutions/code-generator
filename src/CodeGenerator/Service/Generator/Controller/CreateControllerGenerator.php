<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Context;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Configurator\Controller\CreateControllerConfigurator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Generator\Dto\DtoGenerator;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\Provider\ContextProvider;

class CreateControllerGenerator extends BaseControllerGenerator
{
    protected const string DTO_SUFFIX = 'CreateRequest';

    public function generate(Context $context): void
    {
        $dtoFilepath = (new DtoGenerator())->generate($context, '', static::DTO_SUFFIX);
        $dtoNamespace = ContextProvider::pathToNamespace($dtoFilepath);
        $dtoNamespace = str_replace('.php', '', $dtoNamespace);

        $context->defaultUseStatements[] = $dtoNamespace;

        parent::generate($context);
    }

    protected function createConfigurator(): CreateControllerConfigurator
    {
        return new CreateControllerConfigurator();
    }
}
