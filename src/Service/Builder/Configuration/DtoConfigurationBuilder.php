<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\Builder\Configuration;

use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Dto\DtoPropertyConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\UseStatementConfiguration;
use JtcSolutions\CodeGenerator\Dto\MappedProperty\MappedProperty;
use JtcSolutions\CodeGenerator\Exception\ConfigurationException;
use JtcSolutions\Helpers\Helper\FQCNHelper;

class DtoConfigurationBuilder extends BaseConfigurationBuilder
{
    protected const string USE_STATEMENT = 'useStatements';

    protected const string INTERFACE = 'interface';

    protected const string PROPERTY = 'property';

    /**
     * @param array<int, DtoPropertyConfiguration> $properties
     * @param array<int, UseStatementConfiguration> $useStatements
     * @param array<int, string> $interfaces
     */
    public function __construct(
        protected readonly string $className,
        protected readonly string $namespace,
        protected array $properties = [],
        protected array $useStatements = [],
        protected array $interfaces = [],
    ) {
    }

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
     * @throws ConfigurationException
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
     * @param class-string $fqcn
     * @throws ConfigurationException
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
     * @param class-string $interface
     * @throws ConfigurationException
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
