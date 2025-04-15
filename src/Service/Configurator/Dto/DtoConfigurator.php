<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Dto;

use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\DtoConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\ClassPropertyMapper;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class DtoConfigurator
{
    public function __construct(
        private readonly ContextProvider $contextProvider,
        private readonly ClassPropertyMapper $classPropertyMapper,
    ) {
    }

    public function configure(
        string $classFullyQualifiedClassName,
        string $prefix = '',
        string $suffix = '',
    ): DtoConfiguration {
        $className = FQCNHelper::transformFQCNToShortClassName($classFullyQualifiedClassName);
        $dtoClassName = $prefix . $className . $suffix;

        $builder = new DtoConfigurationBuilder(
            className: $dtoClassName,
            namespace: $this->contextProvider->getDtoNamespace($classFullyQualifiedClassName),
        );

        foreach ($this->contextProvider->dtoInterfaces as $dtoInterface) {
            $builder->addUseStatement($dtoInterface);
            $builder->addInterface(FQCNHelper::transformFQCNToShortClassName($dtoInterface));
        }

        $propertyMap = $this->classPropertyMapper->getPropertyMap($classFullyQualifiedClassName);
        foreach ($propertyMap as $property) {
            $builder->addProperty($property);
        }

        return $builder->build();
    }
}
