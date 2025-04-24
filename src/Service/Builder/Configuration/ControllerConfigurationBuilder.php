<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\IOpenApiDocConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;

/**
 * @extends BaseClassConfigurationBuilder<ControllerConfiguration>
 * Builder for creating ControllerConfiguration objects.
 * Provides methods to add use statements, extended classes, interfaces, OpenAPI docs,
 * constructor parameters, and define the main method.
 */
class ControllerConfigurationBuilder extends BaseClassConfigurationBuilder
{
    /**
     * @const string Type identifier for OpenAPI documentation items.
     */
    protected const string OPEN_API_DOC = 'openApiDoc';

    /**
     * @const string Type identifier for implemented interfaces.
     */
    protected const string INTERFACE = 'interface';

    /**
     * @var array<int, IOpenApiDocConfiguration> Stores OpenAPI doc configurations, keyed by order index.
     */
    protected array $openApiDocs = [];

    /**
     * @param string $className The short name of the controller class to be built.
     * @param string $namespace The namespace for the controller class.
     * @param MethodConfiguration|null $method Optional configuration for the main method of the controller.
     * @param bool $callParent Whether the generated constructor should call parent::__construct().
     * @param string|null $constructorBody Optional custom code snippet for the constructor body.
     * @param array<int, UseStatementConfiguration> $useStatements Initial use statements.
     * @param array<int, string> $interfaces Initial interfaces (short names).
     */
    public function __construct(
        string $className,
        string $namespace,
        protected readonly ?MethodConfiguration $method = null,
        bool $callParent = false,
        protected readonly ?string $constructorBody = null,
        array $interfaces = [],
        array $useStatements = [],
        array $extends = [],
    ) {
        parent::__construct($className, $namespace, $useStatements, $interfaces, $extends, [], $callParent);
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
}
