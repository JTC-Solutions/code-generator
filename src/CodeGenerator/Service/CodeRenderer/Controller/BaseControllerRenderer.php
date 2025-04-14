<?php declare(strict_types = 1);

namespace JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\Controller;

use JtcSolutions\CodeGenerator\CodeGenerator\Dto\Configuration\Controller\ControllerConfiguration;
use JtcSolutions\CodeGenerator\CodeGenerator\Service\CodeRenderer\BaseRenderer;

/** @property ControllerConfiguration $configuration */
abstract class BaseControllerRenderer extends BaseRenderer
{
    protected function addOpenApiDoc(): void
    {
        foreach ($this->configuration->openApiDocs as $doc) {
            $this->code .= $doc->render() . "\n"; // TODO: Maybe add open api render that will handle it
        }
    }

    protected function addConstructor(): void
    {
        if ($this->configuration->constructorParams !== []) {
            $this->code .= "    public function __construct(\n";

            $paramStrings = [];
            $paramNames = [];

            foreach ($this->configuration->constructorParams as $param) {
                $paramStrings[] = "        {$param->argumentType} \${$param->argumentName}";
                $paramNames[] = "\${$param->argumentName}";
            }

            $this->code .= implode(",\n", $paramStrings) . "\n";
            $this->code .= "    ) {\n";

            if ($this->configuration->callParent) {
                $this->code .= "        parent::__construct(" . implode(", ", $paramNames) . ");\n";
            }

            if ($this->configuration->constructorBody !== null) {
                $this->code .= "        {$this->configuration->constructorBody}\n";
            }

            $this->code .= "    }\n\n";
        }
    }

    protected function addMethodAttributes(): void
    {
        if (
            $this->configuration->methodConfiguration === null
            || $this->configuration->methodConfiguration->attributes === []
        ) {
            return;
        }

        foreach ($this->configuration->methodConfiguration->attributes as $attribute) {
            $this->code .= '    ' . $attribute->render() . "\n";
        }
    }

    protected function addMethodName(): void
    {
        if (
            $this->configuration->methodConfiguration === null
        ) {
            return;
        }

        $this->code .= "    public function {$this->configuration->methodConfiguration->name}(\n";
    }

    protected function addMethodArguments(): void
    {
        if (
            $this->configuration->methodConfiguration === null
        ) {
            return;
        }

        $argStrings = [];

        foreach ($this->configuration->methodConfiguration->arguments as $arg) {
            $argStrings[] = "        {$arg->argumentType} \${$arg->argumentName}";
        }

        if ($argStrings !== []) {
            $this->code .= implode(",\n", $argStrings) . "\n";
        }

        $this->code .= "    ): {$this->configuration->methodConfiguration->returnType} {\n";
    }

    protected function addMethodBody(): void
    {
        if ($this->configuration->methodConfiguration === null) {
            return;
        }

        $methodLine = explode("\n", $this->configuration->methodConfiguration->methodBody);

        foreach ($methodLine as $line) {
            $this->code .= "        {$line}\n";
        }

        $this->code .= "    }\n";

        $this->code .= "}\n";
    }
}
