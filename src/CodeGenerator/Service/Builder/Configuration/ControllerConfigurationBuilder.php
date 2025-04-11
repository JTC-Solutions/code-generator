<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodArgumentConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\Method\MethodConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\OpenApiDoc\IOpenApiDocConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class ControllerConfigurationBuilder extends BaseConfigurationBuilder
{
    protected const string USE_STATEMENT = 'useStatements';

    protected const string OPEN_API_DOC = 'openApiDoc';

    protected const string INTERFACE = 'interface';

    protected const string CONSTRUCTOR_PARAM = 'constructorParam';

    protected const string EXTENDED_CLASS = 'extendedClass';

    /**
     * @var array<int, string>
     */
    protected array $extends = [];

    /**
     * @var array<int, string>
     */
    protected array $useStatements = []; // TODO: Add option to import as "as"

    /**
     * @var array<int, IOpenApiDocConfiguration>
     */
    protected array $openApiDocs = [];

    /**
     * @var array<int, string>
     */
    protected array $interfaces = [];

    /**
     * @var array<int, MethodArgumentConfiguration>
     */
    protected array $constructorParams = [];

    protected ?string $constructorBody = null; // TODO: maybe by default pass parent arguments ?

    public function __construct(
        protected readonly string $className,
        protected readonly string $namespace,
        protected readonly ?MethodConfiguration $method = null,
    ) {
    }

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
        );
    }

    /**
     * @throws ConfigurationException
     */
    public function addUseStatement(string $useStatement, ?int $order = null): self
    {
        /** @var array<int,string> $result */
        $result = $this->addItem(self::USE_STATEMENT, $useStatement, $this->useStatements, $order);

        $this->useStatements = $result;

        return $this;
    }

    /**
     * @param class-string $extendedClass
     * @throws ConfigurationException
     */
    public function addExtendedClass(string $extendedClass, ?int $order = null): self
    {
        // add parent to use statements
        if ($this->itemExists($extendedClass, $this->useStatements) === false) {
            $this->addUseStatement($extendedClass);
        }

        $extendedClassName = FQCNHelper::transformFQCNToEntityName($extendedClass, false);

        /** @var array<int,string> $result */
        $result = $this->addItem(self::EXTENDED_CLASS, $extendedClassName, $this->extends, $order);

        $this->extends = $result;

        return $this;
    }

    /**
     * @throws ConfigurationException
     */
    public function addOpenApiDoc(IOpenApiDocConfiguration $openApiDoc, ?int $order = null): self
    {
        /** @var array<int, IOpenApiDocConfiguration> $result */
        $result = $this->addItem(self::OPEN_API_DOC, $openApiDoc, $this->openApiDocs, $order);

        $this->openApiDocs = $result;

        return $this;
    }

    /**
     * @throws ConfigurationException
     */
    public function addInterface(string $interface, ?int $order = null): self
    {
        /** @var array<int,string> $result */
        $result = $this->addItem(self::INTERFACE, $interface, $this->interfaces, $order);

        $this->interfaces = $result;

        return $this;
    }

    /**
     * @throws ConfigurationException
     */
    public function addConstructorParam(MethodArgumentConfiguration $constructorParam, ?int $order = null): self
    {
        /** @var array<int,MethodArgumentConfiguration> $result */
        $result = $this->addItem(self::CONSTRUCTOR_PARAM, $constructorParam, $this->constructorParams, $order);

        $this->constructorParams = $result;

        return $this;
    }
}
