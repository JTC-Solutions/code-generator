<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Repository\RepositoryConfiguration;

/**
 * @extends BaseClassConfigurationBuilder<RepositoryConfiguration>
 */
class RepositoryConfigurationBuilder extends BaseClassConfigurationBuilder
{
    public function build(): RepositoryConfiguration
    {
        parent::sortArrays();

        return new RepositoryConfiguration(
            className: $this->className,
            namespace: $this->namespace,
            extends: $this->extends,
            useStatements: $this->useStatements,
            interfaces: $this->interfaces,
            constructorParams: $this->constructorParams,
        );
    }
}
