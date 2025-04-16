<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Configurator\Dto;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\CodeGenerator\Service\Builder\Configuration\DtoConfigurationBuilder;
use JtcSolutions\CodeGenerator\Service\PropertyMapper\ClassPropertyMapper;
use JtcSolutions\CodeGenerator\Service\Provider\ContextProvider;
use JtcSolutions\Helpers\Helper\FQCNHelper;
use ReflectionException;

/**
 * Configures Data Transfer Object (DTO) classes.
 * Uses ClassPropertyMapper to determine properties based on an entity class
 * and builds the DTO configuration using DtoConfigurationBuilder.
 */
class DtoConfigurator
{
    /**
     * @param ContextProvider $contextProvider Provides context like namespaces and paths.
     * @param ClassPropertyMapper $classPropertyMapper Service to map properties from the entity class.
     * @param class-string $requestDtoInterface Default interface to be added to generated Request DTOs.
     * @param string[] $ignoredProperties Properties that will be skipped in every dto
     */
    public function __construct(
        private readonly ContextProvider $contextProvider,
        private readonly ClassPropertyMapper $classPropertyMapper,
        private readonly string $requestDtoInterface,
        private readonly array $ignoredProperties,
    ) {
    }

    /**
     * Configures the DTO structure based on a target entity class.
     *
     * @param class-string $classFullyQualifiedClassName The FQCN of the entity to base the DTO on.
     * @param string $prefix Optional prefix for the generated DTO class name.
     * @param string $suffix Optional suffix for the generated DTO class name.
     * @return DtoConfiguration The configured DTO structure DTO.
     * @throws ReflectionException If reflection on the entity class fails.
     * @throws ConfigurationException If building the DTO configuration fails.
     * @throws Exception If FQCN parsing fails.
     */
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

        $builder->addInterface($this->requestDtoInterface);

        foreach ($this->contextProvider->dtoInterfaces as $dtoInterface) {
            $builder->addInterface($dtoInterface);
        }

        $propertyMap = $this->classPropertyMapper->getPropertyMap($classFullyQualifiedClassName);
        foreach ($propertyMap as $property) {
            if (! in_array($property->name, $this->ignoredProperties, true)) {
                $builder->addProperty($property);
            }
        }

        return $builder->build();
    }
}
