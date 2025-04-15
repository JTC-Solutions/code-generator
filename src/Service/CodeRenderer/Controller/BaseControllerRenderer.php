<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\Service\CodeRenderer\BaseRenderer;

abstract class BaseControllerRenderer extends BaseRenderer
{
    protected function addOpenApiDoc(ControllerConfiguration $configuration): void
    {
        foreach ($configuration->openApiDocs as $doc) {
            $this->code .= $doc->render() . "\n"; // TODO: Maybe add open api render that will handle it
        }
    }

    protected function addConstructor(ControllerConfiguration $configuration): void
    {
        if ($configuration->constructorParams !== []) {
            $this->code .= "    public function __construct(\n";

            $paramStrings = [];
            $paramNames = [];

            foreach ($configuration->constructorParams as $param) {
                $paramStrings[] = "        {$param->argumentType} \${$param->argumentName}";
                $paramNames[] = "\${$param->argumentName}";
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

    protected function addMethodAttributes(ControllerConfiguration $configuration): void
    {
        if (
            $configuration->methodConfiguration === null
            || $configuration->methodConfiguration->attributes === []
        ) {
            return;
        }

        foreach ($configuration->methodConfiguration->attributes as $attribute) {
            $this->code .= '    ' . $attribute->render() . "\n";
        }
    }

    protected function addMethodName(ControllerConfiguration $configuration): void
    {
        if (
            $configuration->methodConfiguration === null
        ) {
            return;
        }

        $this->code .= "    public function {$configuration->methodConfiguration->name}(\n";
    }

    protected function addMethodArguments(ControllerConfiguration $configuration): void
    {
        if (
            $configuration->methodConfiguration === null
        ) {
            return;
        }

        $argStrings = [];

        foreach ($configuration->methodConfiguration->arguments as $arg) {
            $argStrings[] = "        {$arg->argumentType} \${$arg->argumentName}";
        }

        if ($argStrings !== []) {
            $this->code .= implode(",\n", $argStrings) . "\n";
        }

        $this->code .= "    ): {$configuration->methodConfiguration->returnType} {\n";
    }

    protected function addMethodBody(ControllerConfiguration $configuration): void
    {
        if ($configuration->methodConfiguration === null) {
            return;
        }

        $methodLine = explode("\n", $configuration->methodConfiguration->methodBody);

        foreach ($methodLine as $line) {
            $this->code .= "        {$line}\n";
        }

        $this->code .= "    }\n";

        $this->code .= "}\n";
    }
}
