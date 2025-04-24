<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Repository;

use Doctrine\ORM\EntityManagerInterface;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Repository\RepositoryConfiguration;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\RepositoryConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Core\Repository\BaseRepository;
use JtcSolutions\Core\Repository\IEntityRepository;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class RepositoryConfigurator
{
    public function __construct(
        private readonly ContextProvider $contextProvider,
    ) {
    }

    /**
     * @param class-string $classFullyQualifiedClassName
     */
    public function configure(
        string $classFullyQualifiedClassName,
    ): RepositoryConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $repositoryClassName = $className . 'Repository';

        $builder = new RepositoryConfigurationBuilder(
            className: $repositoryClassName,
            namespace: $this->contextProvider->getRepositoryNamespace($classFullyQualifiedClassName),
        );

        $builder->addInterface(IEntityRepository::class);
        $builder->addExtendedClass(BaseRepository::class);

        $builder->addConstructorParam(new MethodArgumentConfiguration(
            argumentName: 'registry',
            argumentType: FQCNHelper::transformFQCNToShortClassName(EntityManagerInterface::class),
        ));

        $builder->addUseStatement(EntityManagerInterface::class);
        $builder->addUseStatement($classFullyQualifiedClassName);

        return $builder->build();
    }
}
