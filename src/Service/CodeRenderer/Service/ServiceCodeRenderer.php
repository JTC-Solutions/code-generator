<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Service;

use JtcSolutions\CodeGenerator\Dto\Configuration\IRenderableConfiguration;
use JtcSolutions\CodeGenerator\Dto\Configuration\Service\ServiceConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\BaseRenderer;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\ICodeRenderer;

class ServiceCodeRenderer extends BaseRenderer implements ICodeRenderer
{
    /**
     * @param ServiceConfiguration $configuration
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


        foreach ($configuration->methodConfigurations as $methodConfiguration) {
            $this->addMethod($methodConfiguration);
        }

        $this->code .= "}\n";

        return $this->code;
    }

    /**
     * Adds the constructor method to the code string if constructor parameters are defined.
     * Handles promoted properties syntax and optional parent::__construct call.
     *
     * @param ServiceConfiguration $configuration Configuration containing constructor parameters and settings.
     */
    protected function addConstructor(ServiceConfiguration $configuration): void
    {
        if ($configuration->constructorParams !== []) {
            $this->code .= "    public function __construct(\n";

            $paramStrings = [];
            $paramNames = [];

            foreach ($configuration->constructorParams as $param) {
                // not promoted
                if ($param->propertyType === null) {
                    $paramStrings[] = "        {$param->argumentType} \${$param->argumentName}";
                    $paramNames[] = "\${$param->argumentName}";
                } elseif ($param->readonly === false) {
                    $paramStrings[] = "        {$param->propertyType} {$param->argumentType} \${$param->argumentName}";
                } else {
                    $paramStrings[] = "        {$param->propertyType} readonly {$param->argumentType} \${$param->argumentName}";
                }
            }

            $this->code .= implode(",\n", $paramStrings) . "\n";
            $this->code .= "    ) {\n";

            if ($configuration->callParent) {
                $this->code .= '        parent::__construct(' . implode(', ', $paramNames) . ");\n";
            }

            if ($configuration->constructorBody !== null) {
                $this->code .= "        {$configuration->constructorBody}\n";
            }

            $this->code .= "    }\n\n";
        }
    }
}
