<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Generator\Repository;

use JtcSolutions\CodeGenerator\Service\CodeRenderer\Repository\RepositoryCodeRenderer;
use JtcSolutions\CodeGenerator\Service\Configurator\Repository\RepositoryConfigurator;
use JtcSolutions\CodeGenerator\Service\Writer\Repository\RepositoryClassWriter;
use JtcSolutions\Core\Entity\IEntity;

class RepositoryGenerator
{
    public function __construct(
        protected readonly RepositoryClassWriter $classWriter,
        protected readonly RepositoryConfigurator $configurator,
        protected readonly RepositoryCodeRenderer $codeRenderer,
    ) {
    }

    /**
     * @param class-string<IEntity> $classFullyQualifiedClassName
     * @return class-string
     */
    public function generate(string $classFullyQualifiedClassName): string
    {
        $configuration = $this->configurator->configure($classFullyQualifiedClassName);
        $code = $this->codeRenderer->renderCode($configuration);

        $serviceFullyQualifiedClassName = $configuration->getFullyQualifiedClassName();

        $this->classWriter->write($classFullyQualifiedClassName, $serviceFullyQualifiedClassName, $code);

        return $serviceFullyQualifiedClassName;
    }
}
