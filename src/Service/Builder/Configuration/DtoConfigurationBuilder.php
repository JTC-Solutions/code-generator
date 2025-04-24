<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoPropertyConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;

/**
 * @extends BaseClassConfigurationBuilder<DtoConfiguration>
 * Builder for creating DtoConfiguration objects.
 * Provides methods to add properties, use statements, and implemented interfaces.
 */
class DtoConfigurationBuilder extends BaseClassConfigurationBuilder
{
    /**
     * @const string Type identifier for DTO properties.
     */
    protected const string PROPERTY = 'property';

    /**
     * @var array<int, DtoPropertyConfiguration> Stores DTO property configurations, keyed by order index.
     */
    protected array $properties = [];

    /**
     * @param string $className The short name of the DTO class to be built.
     * @param string $namespace The namespace for the DTO class.
     * @param array<int, DtoPropertyConfiguration> $properties Initial properties.
     * @param array<int, UseStatementConfiguration> $useStatements Initial use statements.
     * @param array<int, string> $interfaces Initial interfaces (short names).
     */
    public function __construct(
        string $className,
        string $namespace,
        array $properties = [],
        array $useStatements = [],
        array $interfaces = [],
        array $extends = [],
    ) {
        $this->properties = $properties;
        $this->interfaces = $interfaces;

        parent::__construct($className, $namespace, $useStatements, $interfaces, $extends);
    }

    /**
     * Builds the final DtoConfiguration object.
     * Sorts collected items (use statements, interfaces, properties) by their order index.
     * Note: DTOs built here do not extend any class by default.
     *
     * @return DtoConfiguration The fully configured DTO.
     */
    public function build(): DtoConfiguration
    {
        ksort($this->useStatements);
        ksort($this->interfaces);
        ksort($this->properties);

        return new DtoConfiguration(
            namespace: $this->namespace,
            className: $this->className,
            useStatements: $this->useStatements,
            extends: [],
            interfaces: $this->interfaces,
            properties: $this->properties,
        );
    }

    /**
     * Adds a property to the DTO configuration.
     * Automatically adds a use statement if the property type requires one (based on MappedProperty).
     *
     * @param MappedProperty $property The mapped property containing name, type, and optional use statement FQCN.
     * @param int|null $order Optional order index for the property.
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the property (by name) already exists or the order index is taken, or if adding the use statement fails.
     */
    public function addProperty(MappedProperty $property, ?int $order = null): self
    {
        $propertyConfiguration = new DtoPropertyConfiguration($property->name, $property->type, $property->nullable);
        if ($property->useStatement !== null) {
            $this->addUseStatement($property->useStatement);
        }

        /** @var array<int, DtoPropertyConfiguration> $result */
        $result = $this->addItem(self::PROPERTY, $propertyConfiguration, $this->properties, $order);

        $this->properties = $result;

        return $this;
    }
}
