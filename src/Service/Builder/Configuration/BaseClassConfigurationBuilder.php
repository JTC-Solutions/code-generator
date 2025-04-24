<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\IConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\Helpers\Helper\FQCNHelper;

/**
 * @template TConfiguration of IConfiguration|IRenderableConfiguration
 * @extends BaseConfigurationBuilder<TConfiguration>
 */
abstract class BaseClassConfigurationBuilder extends BaseConfigurationBuilder
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
     * @const string Type identifier for extended classes.
     */
    protected const string EXTENDED_CLASS = 'extendedClass';

    /**
     * @const string Type identifier for constructor parameters.
     */
    protected const string CONSTRUCTOR_PARAM = 'constructorParam';

    /**
     * @param string $className The short name of the controller class to be built.
     * @param string $namespace The namespace for the controller class.
     * @param array<int, UseStatementConfiguration> $useStatements Stores use statement configurations, keyed by order index.
     * @param array<int, string> $interfaces Stores the short names of implemented interfaces, keyed by order index.
     * @param array<int, string> $extends Stores the short names of extended classes.
     * @param array<int, MethodArgumentConfiguration> $constructorParams Stores constructor parameter configurations, keyed order index.
     * @param bool $callParent Whether the generated constructor should call parent::__construct().
     */
    public function __construct(
        protected readonly string $className,
        protected readonly string $namespace,
        protected array $useStatements = [],
        protected array $interfaces = [],
        protected array $extends = [],
        protected array $constructorParams = [],
        protected bool $callParent = false,
    ) {
    }

    /**
     * Adds a use statement to the configuration.
     *
     * @param class-string $fqcn The fully qualified class name to use.
     * @param string|null $alias Optional alias for the use statement.
     * @param int|null $order Optional order index for the use statement.
     * @return static The builder instance for method chaining.
     */
    public function addUseStatement(string $fqcn, ?string $alias = null, ?int $order = null): self
    {
        $statement = new UseStatementConfiguration($fqcn, $alias);

        /** We do not want to throw exception in case of duplicate use statements */
        try {
            /** @var array<int, UseStatementConfiguration> $result */
            $result = $this->addItem(static::USE_STATEMENT, $statement, $this->useStatements, $order);
        } catch (ConfigurationException $e) {
            return $this;
        }

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
     * @return static The builder instance for method chaining.
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
        $result = $this->addItem(static::INTERFACE, $interfaceClassName, $this->interfaces, $order);

        $this->interfaces = $result;

        return $this;
    }

    /**
     * Adds an extended class to the configuration.
     * Automatically adds a corresponding use statement if not already present.
     * Stores the short class name for the 'extends' clause.
     *
     * @param class-string $extendedClass The fully qualified class name of the parent class.
     * @param int|null $order Optional order index for the extends clause (relevant if multiple inheritance were supported, usually just one).
     * @return static The builder instance for method chaining.
     * @throws ConfigurationException If the extended class (by short name) already exists or the order index is taken, or if adding the use statement fails.
     * @throws Exception If FQCN parsing fails.
     */
    public function addExtendedClass(string $extendedClass, ?int $order = null): self
    {
        // add parent to use statements
        if ($this->itemExists($extendedClass, $this->useStatements) === false) {
            $this->addUseStatement($extendedClass);
        }

        $extendedClassName = FQCNHelper::transformFQCNToShortClassName($extendedClass);

        /** @var array<int,string> $result */
        $result = $this->addItem(self::EXTENDED_CLASS, $extendedClassName, $this->extends, $order);

        $this->extends = $result;

        return $this;
    }

    /**
     * Adds a constructor parameter to the configuration.
     * Automatically adds a use statement for the parameter's type hint if not already present.
     * Stores the parameter configuration with the short type name.
     *
     * @param MethodArgumentConfiguration $constructorParam The constructor parameter configuration.
     * @param int|null $order Optional order index for the constructor parameter.
     * @return static The builder instance for method chaining.
     * @throws ConfigurationException If the parameter (by name) already exists or the order index is taken, or if adding the use statement fails.
     * @throws Exception If FQCN parsing fails.
     */
    public function addConstructorParam(MethodArgumentConfiguration $constructorParam, ?int $order = null): self
    {
        if ($this->itemExists($constructorParam->argumentType, $this->useStatements) === false) {
            //$this->addUseStatement($constructorParam->argumentType);
        }

        if ($constructorParam->mapRequestPayloadAttribute === true) {
            throw ConfigurationException::invalidConfigurationCombination('Constructor parameter cannot have mapRequestPayload attribute.');
        }

        $paramTypeClassName = FQCNHelper::transformFQCNToShortClassName($constructorParam->argumentType);

        $parsed = new MethodArgumentConfiguration(
            argumentName: $constructorParam->argumentName,
            argumentType: $paramTypeClassName,
            mapRequestPayloadAttribute: false,
            propertyType: $constructorParam->propertyType,
            readonly: $constructorParam->readonly,
        );

        /** @var array<int,MethodArgumentConfiguration> $result */
        $result = $this->addItem(self::CONSTRUCTOR_PARAM, $parsed, $this->constructorParams, $order);

        $this->constructorParams = $result;

        return $this;
    }

    /**
     * Sorts all default arrays for classes by their key
     */
    protected function sortArrays(): void
    {
        ksort($this->interfaces);
        ksort($this->useStatements);
        ksort($this->extends);
        ksort($this->constructorParams);
    }
}
