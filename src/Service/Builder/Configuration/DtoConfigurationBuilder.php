<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoPropertyConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\Helpers\Helper\FQCNHelper;

/**
 * Builder for creating DtoConfiguration objects.
 * Provides methods to add properties, use statements, and implemented interfaces.
 */
class DtoConfigurationBuilder extends BaseConfigurationBuilder
{
    /**
     * @const string Type identifier for use statements.
     */
    protected const string USE_STATEMENT = 'useStatements';

    /**
     * @const string Type identifier for implemented interfaces.
     */
    protected const string INTERFACE = 'interface';

    /**
     * @const string Type identifier for DTO properties.
     */
    protected const string PROPERTY = 'property';

    /**
     * @var array<int, DtoPropertyConfiguration> Stores DTO property configurations, keyed by order index.
     */
    protected array $properties = [];

    /**
     * @var array<int, UseStatementConfiguration> Stores use statement configurations, keyed by order index.
     */
    protected array $useStatements = [];

    /**
     * @var array<int, string> Stores the short names of implemented interfaces, keyed by order index.
     */
    protected array $interfaces = [];

    /**
     * @param string $className The short name of the DTO class to be built.
     * @param string $namespace The namespace for the DTO class.
     * @param array<int, DtoPropertyConfiguration> $properties Initial properties.
     * @param array<int, UseStatementConfiguration> $useStatements Initial use statements.
     * @param array<int, string> $interfaces Initial interfaces (short names).
     */
    public function __construct(
        protected readonly string $className,
        protected readonly string $namespace,
        array $properties = [],
        array $useStatements = [],
        array $interfaces = [],
    ) {
        $this->properties = $properties;
        $this->useStatements = $useStatements;
        $this->interfaces = $interfaces;
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
        $propertyConfiguration = new DtoPropertyConfiguration($property->name, $property->type);
        if ($property->useStatement !== null) {
            $this->addUseStatement($property->useStatement);
        }

        /** @var array<int, DtoPropertyConfiguration> $result */
        $result = $this->addItem(self::PROPERTY, $propertyConfiguration, $this->properties, $order);

        $this->properties = $result;

        return $this;
    }

    /**
     * Adds a use statement to the configuration.
     *
     * @param class-string $fqcn The fully qualified class name to use.
     * @param string|null $alias Optional alias for the use statement.
     * @param int|null $order Optional order index for the use statement.
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the use statement (by FQCN) already exists or the order index is taken.
     */
    public function addUseStatement(string $fqcn, ?string $alias = null, ?int $order = null): self
    {
        $statement = new UseStatementConfiguration($fqcn, $alias);

        /** @var array<int, UseStatementConfiguration> $result */
        $result = $this->addItem(self::USE_STATEMENT, $statement, $this->useStatements, $order);

        $this->useStatements = $result;

        return $this;
    }

    /**
     * Adds an implemented interface to the configuration.
     * Automatically adds a corresponding use statement if not already present.
     * Stores the short interface name for the 'implements' clause.
     *
     * @param class-string $interface The fully qualified class name of the interface.
     * @param int|null $order Optional order index for the implements clause.
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the interface (by short name) already exists or the order index is taken, or if adding the use statement fails.
     * @throws Exception If FQCN parsing fails.
     */
    public function addInterface(string $interface, ?int $order = null): self
    {
        try {
            $this->addUseStatement($interface);
        } catch (ConfigurationException $e) {
            // do nothing
        }
        $interfaceClassName = FQCNHelper::transformFQCNToShortClassName($interface);

        /** @var array<int,string> $result */
        $result = $this->addItem(self::INTERFACE, $interfaceClassName, $this->interfaces, $order);

        $this->interfaces = $result;

        return $this;
    }
}
