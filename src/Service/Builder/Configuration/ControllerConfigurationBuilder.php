<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use Exception;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\IOpenApiDocConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\Helpers\Helper\FQCNHelper;

/**
 * Builder for creating ControllerConfiguration objects.
 * Provides methods to add use statements, extended classes, interfaces, OpenAPI docs,
 * constructor parameters, and define the main method.
 */
class ControllerConfigurationBuilder extends BaseConfigurationBuilder
{
    /**
     * @const string Type identifier for use statements.
     */
    protected const string USE_STATEMENT = 'useStatements';

    /**
     * @const string Type identifier for OpenAPI documentation items.
     */
    protected const string OPEN_API_DOC = 'openApiDoc';

    /**
     * @const string Type identifier for implemented interfaces.
     */
    protected const string INTERFACE = 'interface';

    /**
     * @const string Type identifier for constructor parameters.
     */
    protected const string CONSTRUCTOR_PARAM = 'constructorParam';

    /**
     * @const string Type identifier for extended classes.
     */
    protected const string EXTENDED_CLASS = 'extendedClass';

    /**
     * @var array<int, string> Stores the short names of extended classes.
     */
    protected array $extends = [];

    /**
     * @var array<int, UseStatementConfiguration> Stores use statement configurations, keyed by order index.
     */
    protected array $useStatements = [];

    /**
     * @var array<int, IOpenApiDocConfiguration> Stores OpenAPI doc configurations, keyed by order index.
     */
    protected array $openApiDocs = [];

    /**
     * @var array<int, string> Stores the short names of implemented interfaces, keyed by order index.
     */
    protected array $interfaces = [];

    /**
     * @var array<int, MethodArgumentConfiguration> Stores constructor parameter configurations, keyed order index.
     */
    protected array $constructorParams = [];

    /**
     * @param string $className The short name of the controller class to be built.
     * @param string $namespace The namespace for the controller class.
     * @param MethodConfiguration|null $method Optional configuration for the main method of the controller.
     * @param bool $callParent Whether the generated constructor should call parent::__construct().
     * @param string|null $constructorBody Optional custom code snippet for the constructor body.
     */
    public function __construct(
        protected readonly string $className,
        protected readonly string $namespace,
        protected readonly ?MethodConfiguration $method = null,
        protected readonly bool $callParent = false,
        protected readonly ?string $constructorBody = null,
    ) {
    }

    /**
     * Builds the final ControllerConfiguration object.
     * Sorts collected items (use statements, docs, interfaces, params) by their order index before creating the DTO.
     *
     * @return ControllerConfiguration The fully configured controller DTO.
     */
    public function build(): ControllerConfiguration
    {
        ksort($this->useStatements);
        ksort($this->openApiDocs);
        ksort($this->interfaces);
        ksort($this->constructorParams);

        return new ControllerConfiguration(
            $this->className,
            $this->namespace,
            $this->method,
            $this->extends,
            $this->useStatements,
            $this->openApiDocs,
            $this->interfaces,
            $this->constructorParams,
            $this->constructorBody,
            $this->callParent,
        );
    }

    /**
     * Adds a use statement to the configuration.
     *
     * @param string $fqcn The fully qualified class name to use.
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
     * Adds an extended class to the configuration.
     * Automatically adds a corresponding use statement if not already present.
     * Stores the short class name for the 'extends' clause.
     *
     * @param class-string $extendedClass The fully qualified class name of the parent class.
     * @param int|null $order Optional order index for the extends clause (relevant if multiple inheritance were supported, usually just one).
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the extended class (by short name) already exists or the order index is taken, or if adding the use statement fails.
     * @throws Exception If FQCN parsing fails.
     */
    public function addExtendedClass(string $extendedClass, ?int $order = null): self
    {
        // add parent to use statements
        if ($this->itemExists($extendedClass, $this->useStatements) === false) {
            $this->addUseStatement($extendedClass);
        }

        $extendedClassName = FQCNHelper::transformFQCNToShortClassName($extendedClass, false);

        /** @var array<int,string> $result */
        $result = $this->addItem(self::EXTENDED_CLASS, $extendedClassName, $this->extends, $order);

        $this->extends = $result;

        return $this;
    }

    /**
     * Adds an OpenAPI documentation configuration item (e.g., Tag, Response, Parameter).
     *
     * @param IOpenApiDocConfiguration $openApiDoc The OpenAPI configuration object.
     * @param int|null $order Optional order index for the OpenAPI attribute.
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the OpenAPI item (by identifier) already exists or the order index is taken.
     */
    public function addOpenApiDoc(IOpenApiDocConfiguration $openApiDoc, ?int $order = null): self
    {
        /** @var array<int, IOpenApiDocConfiguration> $result */
        $result = $this->addItem(self::OPEN_API_DOC, $openApiDoc, $this->openApiDocs, $order);

        $this->openApiDocs = $result;

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
        /** @var array<int,string> $result */
        $result = $this->addItem(self::INTERFACE, $interface, $this->interfaces, $order);

        $this->interfaces = $result;

        return $this;
    }

    /**
     * Adds a constructor parameter to the configuration.
     * Automatically adds a use statement for the parameter's type hint if not already present.
     * Stores the parameter configuration with the short type name.
     *
     * @param MethodArgumentConfiguration $constructorParam The constructor parameter configuration.
     * @param int|null $order Optional order index for the constructor parameter.
     * @return $this The builder instance for method chaining.
     * @throws ConfigurationException If the parameter (by name) already exists or the order index is taken, or if adding the use statement fails.
     * @throws Exception If FQCN parsing fails.
     */
    public function addConstructorParam(MethodArgumentConfiguration $constructorParam, ?int $order = null): self
    {
        if ($this->itemExists($constructorParam->argumentType, $this->useStatements) === false) {
            $this->addUseStatement($constructorParam->argumentType);
        }

        $paramTypeClassName = FQCNHelper::transformFQCNToShortClassName($constructorParam->argumentType);

        $parsed = new MethodArgumentConfiguration($constructorParam->argumentName, $paramTypeClassName);

        /** @var array<int,MethodArgumentConfiguration> $result */
        $result = $this->addItem(self::CONSTRUCTOR_PARAM, $parsed, $this->constructorParams, $order);

        $this->constructorParams = $result;

        return $this;
    }
}
