<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Repository;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Repository\RepositoryConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\BaseRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\ICodeRenderer;

class RepositoryCodeRenderer extends BaseRenderer implements ICodeRenderer
{
    /**
     * @param RepositoryConfiguration $configuration
     */
    public function renderCode(IRenderableConfiguration $configuration): string
    {
        $this->addDeclareStrictTypes();
        $this->addNamespace($configuration);
        $this->addUseStatements($configuration);
        $this->addClassName($configuration);
        $this->addExtendedClasses($configuration);
        $this->addImplementedInterfaces($configuration);

        $this->code .= "\n{\n";

        $this->addConstructor($configuration);

        $this->code .= "}\n";

        return $this->code;
    }

    /**
     * Adds the constructor with passed class entity to parent repository
     *
     * @param RepositoryConfiguration $configuration The DTO configuration containing properties.
     */
    protected function addConstructor(RepositoryConfiguration $configuration): void
    {
        $className = str_replace('Repository', '', $configuration->getClassName()); // TODO: fix this later

        $this->code .= "    public function __construct(\n";

        foreach ($configuration->constructorParams as $param) {
            $this->code .= sprintf("        %s \$%s,\n", $param->argumentType, $param->argumentName);
        }

        $this->code .= "    ) {\n";

        $this->code .= sprintf("        parent::__construct(\$registry, %s::class);\n", $className);

        $this->code .= "}\n";
    }
}
